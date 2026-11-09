@extends('themes.xylo.layouts.auth')

@section('content')
<div class="login-page min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="apple-auth-card">

        {{-- Logo --}}
        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="apple-auth-logo">

        {{-- Heading --}}
        <h1 class="apple-auth-title text-center">{{ __('store.login.welcome_back') }}</h1>
        <p class="apple-auth-sub text-center">{{ __('store.login.form_subtitle') }}</p>

        {{-- Login Form --}}
        <form method="POST" action="{{ route('customer.login') }}">
            @csrf

            <div class="mb-3">
                <input type="text" name="email" value="{{ old('email') }}"
                       placeholder="{{ __('store.login.email') }}"
                       class="form-control" autocomplete="email">
                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <input type="password" name="password"
                       placeholder="{{ __('store.login.password') }}"
                       class="form-control" autocomplete="current-password">
                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <button type="submit" class="apple-auth-btn">
                {{ __('store.login.login_btn') }}
            </button>
        </form>

        <p class="mt-4 mb-0 text-center">
            {{ __('store.login.dont_have_account') }}
            <a href="{{ route('customer.register') }}" class="apple-auth-link fw-semibold">
                {{ __('store.login.signup') }}
            </a>
            <br>
            <a href="{{ route('customer.password.request') }}" class="apple-auth-link">
                {{ __('store.login.forgot_password') }}
            </a>
        </p>

        <p class="text-center mt-4 mb-0 apple-auth-legal">
            {{ __('store.login.copyright') }}
        </p>
    </div>
</div>
@endsection
