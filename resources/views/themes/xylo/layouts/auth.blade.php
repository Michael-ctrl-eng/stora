<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Laravel') }}</title>
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/sass/app.scss'])
    @endif
    {{-- Self-hosted Inter font (no external origins) --}}
    <link rel="preload" href="{{ asset('vendor/fonts/inter-var-latin.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="{{ asset('vendor/css/inter.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/css/bootstrap.min.css') }}">
        @if (!App::environment('testing'))
            @vite(['resources/views/themes/xylo/css/animate.min.css'])
        @endif
        @if (!App::environment('testing'))
            @vite(['resources/views/themes/xylo/css/slick.css'])
        @endif
        <link rel="stylesheet" href="{{ asset('vendor/css/slick-theme.css') }}">
        <link rel="stylesheet" href="{{ asset('vendor/css/all.min.css') }}" />
        @if (!App::environment('testing'))
            @vite(['resources/views/themes/xylo/css/style.css'])
        @endif
        @if (!App::environment('testing'))
            @vite(['resources/views/themes/xylo/css/custom.css'])
        @endif
        {{-- Apple-compatible design layer : must load after custom.css --}}
        @if (!App::environment('testing'))
            @vite(['resources/views/themes/xylo/css/apple.css'])
        @endif
    @yield('css')
</head>
<body class="apple-theme">
    @yield('content')
    @if (!App::environment('testing'))
        @vite(['resources/views/themes/xylo/js/app.js'])
    @endif
    <script src="{{ asset('vendor/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('vendor/js/bootstrap.bundle.min.js') }}"></script>
    @yield('js')
 
</body>
</html>