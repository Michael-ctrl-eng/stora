@extends('themes.xylo.layouts.auth')

@section('content')
<div class="login-page min-vh-100 d-flex align-items-center justify-content-center py-5">
    <div class="apple-auth-card">

        {{-- Logo --}}
        <img src="{{ asset('images/logo.png') }}" alt="{{ config('app.name') }}" class="apple-auth-logo">

        {{-- Heading --}}
        <h1 class="apple-auth-title text-center">{{ __('store.register.welcome_back') }}</h1>
        <p class="apple-auth-sub text-center">{{ __('store.register.form_subtitle') }}</p>

        {{-- Register Form --}}
        <form method="POST" action="{{ route('customer.register') }}">
            @csrf

            <div class="mb-3">
                <input type="text" name="name" value="{{ old('name') }}"
                       placeholder="{{ __('store.register.name') }}"
                       class="form-control" autocomplete="name">
                @error('name') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <input type="email" name="email" value="{{ old('email') }}"
                       placeholder="{{ __('store.register.email') }}"
                       class="form-control" autocomplete="email">
                @error('email') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mb-3">
                <input type="password" name="password"
                       placeholder="{{ __('store.register.password') }}"
                       class="form-control" autocomplete="new-password">
                @error('password') <span class="text-danger small">{{ $message }}</span> @enderror
            </div>

            <div class="mb-4">
                <input type="password" name="password_confirmation"
                       placeholder="{{ __('store.register.confirm_password') }}"
                       class="form-control" autocomplete="new-password">
            </div>

            <button type="submit" class="apple-auth-btn">
                {{ __('store.register.signup_btn') }}
            </button>
        </form>

        <p class="mt-4 mb-0 text-center">
            {{ __('store.register.already_account') }}
            <a href="{{ route('customer.login') }}" class="apple-auth-link fw-semibold">
                {{ __('store.register.login_here') }}
            </a>
        </p>

        <p class="text-center mt-4 mb-0 apple-auth-legal">
            {{ __('store.register.copyright') }}
        </p>
    </div>
</div>
@endsection
