<p align="center">
  <img src="public/images/logo.png" alt="Stora — beautifully simple commerce" width="72">
</p>

<h1 align="center">Stora</h1>

<p align="center">
  <strong>Everything you love. In one place.</strong><br>
  An Apple-inspired, white-minimal glassmorphism storefront — built on Laravel.
</p>

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-10-FF2D20?logo=laravel&logoColor=white" alt="Laravel 10">
  <img src="https://img.shields.io/badge/PHP-8.1%2B-777BB4?logo=php&logoColor=white" alt="PHP 8.1+">
  <img src="https://img.shields.io/badge/theme-Apple.com%20style-0071e3" alt="Apple.com style theme">
  <img src="https://img.shields.io/badge/deploy-Vercel-000000?logo=vercel" alt="Deploy on Vercel">
</p>

<p align="center">
  <img src="docs/screenshots/readme-hero.png" alt="Stora storefront hero — Apple.com style white minimal theme">
</p>

Stora is a fork of [Velstore](https://github.com/velstorelabs/velstore), the open-source
multi-vendor Laravel eCommerce platform, re-imagined with a premium Apple.com-compatible
theme: alternating white / `#f5f5f7` sections, a translucent glassy sticky navigation
(`backdrop-filter: saturate(180%) blur(20px)`), SF-style typography, pill buttons and
rounded product tiles — with the original logo retained and the store rebranded to **Stora**.

> The seeded catalog contains **real studio product photography only**, curated demo
> brands and honest pricing — no fabricated orders, reviews or revenue numbers anywhere.

## Screenshots

| | |
|---|---|
| ![Categories](docs/screenshots/readme-cats.png) | ![Trending products](docs/screenshots/readme-products.png) |
| ![Product detail](docs/screenshots/readme-pdp.png) | ![Shop](docs/screenshots/readme-shop.png) |

## Features

- Built with Laravel 10
- Apple.com-style storefront theme (glassmorphism, white minimal)
- Multi-vendor support
- Multi-lingual storefront — translated to 19 languages
- Dedicated Admin, Seller, and Customer panels
- Integrated payment gateways: PayPal and Stripe
- SQLite demo catalog included — zero-database quick start
- One-file Vercel deployment (`api/index.php` + `vercel.json`)

### Supported Languages

<p align="center" style="display: inline;">
    <img src="https://flagicons.lipis.dev/flags/4x3/us.svg" title="English" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/de.svg" title="German" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/fr.svg" title="French" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/es.svg" title="Spanish" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/nl.svg" title="Dutch" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/it.svg" title="Italian" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/pt.svg" title="Portuguese" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/in.svg" title="Hindi" width="24">
    &nbsp;&nbsp;&nbsp;
    <img src="https://flagicons.lipis.dev/flags/4x3/pl.svg" title="Polish" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/ru.svg" title="Russian" width="24">
    &nbsp;&nbsp;&nbsp;
    <img src="https://flagicons.lipis.dev/flags/4x3/tr.svg" title="Turkish" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/sa.svg" title="Arabic" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/ir.svg" title="Persian" width="24">
    &nbsp;&nbsp;&nbsp;
    <img src="https://flagicons.lipis.dev/flags/4x3/cn.svg" title="Chinese" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/jp.svg" title="Japanese" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/id.svg" title="Indonesian" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/vi.svg" title="Vietnamese" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/kr.svg" title="Korean" width="24">
    <img src="https://flagicons.lipis.dev/flags/4x3/th.svg" title="Thai" width="24">
</p>

## Deploy to Vercel

The repo ships with a ready-made serverless layer (`api/index.php`, `vercel.json`,
`.vercelignore`) powered by the community [vercel-php](https://github.com/vercel-community/php)
runtime (PHP 8.3). The seeded SQLite catalog is bundled with the deployment and
provisioned to a writable `/tmp` copy per instance — no external database required.

[![Deploy with Vercel](https://vercel.com/button)](https://vercel.com/new/clone?repository-url=https%3A%2F%2Fgithub.com%2FMichael-ctrl-eng%2Fstora&project-name=stora&repository-name=stora)

All required environment variables (including `APP_KEY`, SQLite path, cookie sessions,
`/tmp` caches) are pre-configured in `vercel.json` — the deployment needs **no manual
setup**. Just import the repository at [vercel.com/new](https://vercel.com/new) and press
Deploy.

> `vercel.json` pins `"framework": null` so Vercel never applies the Vite preset
> (which expects a `dist/` output). The compiled assets already live in
> `public/build` and the PHP runtime installs Composer dependencies itself.

### Demo accounts

| Panel | URL | Email | Password |
| --- | --- | --- | --- |
| Admin | `/login` | `admin@stora.store` | `stora123` |
| Seller | `/vendor/login` | `seller@example.com` | `abc123` |

> Change these before going live — they are demo credentials for the seeded catalog.

> Vercel Hobby bandwidth applies; static assets are served through the PHP function.

## Local Development

```sh
# 1. PHP dependencies
composer install

# 2. Environment (SQLite by default — no MySQL needed for the demo)
cp .env.example .env
php artisan key:generate
touch database/database.sqlite

# 3. Schema + demo catalog (products, categories, brands, banners — no fake orders)
php artisan migrate:fresh --seed

# 4. Frontend assets
npm install && npm run build

# 5. Serve
php artisan serve
```

Open [http://127.0.0.1:8000](http://127.0.0.1:8000).

For MySQL, point `DB_CONNECTION` / `DB_HOST` / `DB_DATABASE` in `.env` at your database
and re-run the migrations.

### Simulate the Vercel runtime locally

The exact launcher the runtime uses:

```sh
DB_CONNECTION=sqlite DB_DATABASE=/tmp/stora.sqlite SESSION_DRIVER=cookie CACHE_DRIVER=array \
FILESYSTEM_DISK=public LOG_CHANNEL=stderr APP_KEY=base64:XXNk2la7ErHb/WeT859NUQVk7vMqK9cirDpt1hS6FNA= \
VIEW_COMPILED_PATH=/tmp/storage/framework/views \
php -S 127.0.0.1:8080 -t public api/index.php
```

## Tech Stack

- Backend: Laravel 10 (PHP 8.1+)
- Database: MySQL (self-hosted) / SQLite (demo, Vercel)
- Frontend: Blade + Vite, custom `apple.css` design layer
- Authentication: Laravel Sanctum / Laravel UI
- DataTables: Yajra Laravel Datatables
- Deployment: any PHP host, or Vercel via vercel-php

## Credits & License

Stora is a rebranded, re-themed fork of
[Velstore](https://github.com/velstorelabs/velstore) by
[Velstore Labs](https://github.com/velstorelabs) — released under the same license
(see [LICENSE](LICENSE)). Storefront photography and product renders are demo assets
created for this project.
