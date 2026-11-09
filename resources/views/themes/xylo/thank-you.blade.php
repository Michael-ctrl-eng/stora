@extends('themes.xylo.layouts.master')

@section('content')
    <section class="thank-you-page pb-5 pt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-7">
                    <div class="cart-box text-center p-5">
                        <div class="thank-you-icon mb-4">
                            <svg width="72" height="72" viewBox="0 0 72 72" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <circle cx="36" cy="36" r="35" stroke="#0071e3" stroke-width="2"/>
                                <path d="M22 37.5 31.5 47 50 26.5" stroke="#0071e3" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </div>
                        <h1 class="cart-heading">{{ __('store.thank_you.title') }}</h1>
                        <p class="mt-3 mb-4">{{ __('store.thank_you.message') }}</p>
                        <p class="small text-muted">{{ __('store.thank_you.email_note') }}</p>
                        <div class="mt-4">
                            <a href="{{ route('shop.index') }}" class="btn btn-primary px-4">{{ __('store.thank_you.continue_shopping') }}</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
