<?php

/**
 * Stora — Vercel serverless entrypoint.
 *
 * The vercel-php runtime boots the PHP built-in development server with
 * this file as the router script and `public/` as the document root
 * (VERCEL_PHP_DOCROOT=public):
 *
 *     php -S 0.0.0.0:3000 -t public api/index.php
 *
 * Contract of a router script: returning `false` lets the built-in
 * server stream the requested file straight from the document root,
 * everything else is handled here (Laravel).
 */

declare(strict_types=1);

// ---------------------------------------------------------------------------
// 1. Writable runtime storage (AWS Lambda only exposes /tmp)
// ---------------------------------------------------------------------------
$tmp = sys_get_temp_dir();

$writableDirs = [
    $tmp . '/storage/framework/views',
    $tmp . '/storage/framework/cache/data',
    $tmp . '/storage/framework/sessions',
    $tmp . '/storage/logs',
];
foreach ($writableDirs as $dir) {
    if (!is_dir($dir)) {
        @mkdir($dir, 0777, true);
    }
}

// ---------------------------------------------------------------------------
// 2. Provision the SQLite database (seeded catalog ships with the repo,
//    each lambda instance gets its own writable copy in /tmp)
// ---------------------------------------------------------------------------
$bundledDb = __DIR__ . '/../database/stora.sqlite';
$runtimeDb = $tmp . '/stora.sqlite';

if (is_file($bundledDb) && !is_file($runtimeDb)) {
    @copy($bundledDb, $runtimeDb);
}

// ---------------------------------------------------------------------------
// 3. Runtime environment
// ---------------------------------------------------------------------------
// NOTE: APP_URL is intentionally NOT forced from VERCEL_URL. Forcing it made
// every route()/asset() URL point at the per-deployment domain while visitors
// browse the production alias — turning same-origin fetches (add to cart,
// checkout, CSRF) into broken cross-origin requests. Laravel derives correct
// same-origin URLs from the incoming request host on its own.

// ---------------------------------------------------------------------------
// 4. Serve static assets directly from the document root (public/)
// ---------------------------------------------------------------------------
$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
if ($uri !== '/' && $uri !== '/api/index.php') {
    $publicRoot = realpath(__DIR__ . '/../public');
    $file = realpath(__DIR__ . '/../public' . $uri);

    // Containment check prevents path traversal; existing files are
    // streamed by the built-in server with correct MIME types.
    if ($file !== false && $publicRoot !== false && is_file($file)
        && str_starts_with($file, $publicRoot . DIRECTORY_SEPARATOR)) {
        return false;
    }
}

// Direct requests for the serverless entrypoint itself are not a page.
if ($uri === '/api/index.php') {
    http_response_code(404);
    exit('Not Found');
}

// ---------------------------------------------------------------------------
// 5. Boot the Laravel application
// ---------------------------------------------------------------------------
require __DIR__ . '/../public/index.php';
