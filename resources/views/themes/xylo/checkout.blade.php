@extends('themes.xylo.layouts.master')
@section('css')
@endsection
@section('content')
    @php $currency = activeCurrency(); @endphp
    <section class="banner-area inner-banner pt-5 animate__animated animate__fadeIn productinnerbanner">
        <div class="container h-100">
            <div class="row">
                <div class="col-md-4">
                    <div class="breadcrumbs">
                        <a href="#">{{ __('store.checkout.breadcrumb_home') }}</a> <i class="fa fa-angle-right"></i> <a href="#">{{ __('store.checkout.breadcrumb_category') }}</a> <i
                            class="fa fa-angle-right"></i>{{ __('store.checkout.breadcrumb_checkout') }}
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="cart-page pb-5 pt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-7">
                    <form id="checkout-form" method="POST" action="{{ route('checkout.process') }}">
                        @csrf

                        <!-- Shipping Information -->
                        <div class="shipping_info">
                            <h3 class="cart-heading">{{ __('store.checkout.shipping_information') }}</h3>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <input type="text" name="first_name" class="form-control" placeholder="{{ __('store.checkout.first_name') }}" required>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <input type="text" name="last_name" class="form-control" placeholder="{{ __('store.checkout.last_name') }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12 mt-3">
                                    <input type="text" name="address" class="form-control" placeholder="{{ __('store.checkout.address') }}" required>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <input type="text" name="suite" class="form-control" placeholder="{{ __('store.checkout.suite') }}">
                                </div>
                                <div class="col-md-6 mt-3">
                                    <select name="country" class="form-select" required>
                                        <option value="">{{ __('store.checkout.select_country') }}</option>
                                        <option value="1">United States</option>
                                        <!-- Dynamically populate -->
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <input type="text" name="city" class="form-control" placeholder="{{ __('store.checkout.city') }}" required>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <select name="state" class="form-select" required>
                                        <option value="">{{ __('store.checkout.select_state') }}</option>
                                        <option value="1">New York</option>
                                        <!-- Dynamically populate -->
                                    </select>
                                </div>
                                <div class="col-md-3 mt-3">
                                    <input type="text" name="zipcode" class="form-control" placeholder="{{ __('store.checkout.zipcode') }}" required>
                                </div>
                            </div>
                            <div class="mt-3">
                                <label>
                                    <input type="checkbox" name="use_as_billing" value="1" checked>{{ __('store.checkout.use_as_billing') }}
                                </label>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="shipping_info">
                            <h3 class="cart-heading mt-5">{{ __('store.checkout.contact_information') }}</h3>
                            <div class="row">
                                <div class="col-md-6 mt-3">
                                    <input type="email" name="email" class="form-control" placeholder="{{ __('store.checkout.email') }}" required>
                                </div>
                                <div class="col-md-6 mt-3">
                                    <input type="text" name="phone" class="form-control" placeholder="{{ __('store.checkout.phone') }}" required>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Method -->
                        <div class="shipping_info mt-5">
                            <h3 class="cart-heading">{{ __('store.checkout.payment_method') }}</h3>

                            @foreach($paymentGateways as $gateway)
                                <div class="form-check mt-2">
                                    <input type="radio" name="gateway" value="{{ $gateway->code }}" 
                                        id="gateway-{{ $gateway->id }}" required>
                                    <label for="gateway-{{ $gateway->id }}">{{ $gateway->name }}</label>
                                </div>

                                @if($gateway->code === 'paypal')
                                    <div id="paypal-button-container" class="mt-3" style="display: none;"></div>
                                @endif

                                @if($gateway->code === 'stripe')
                                    <div id="card-element" class="mt-3" style="display: none;"></div>
                                    <div id="card-errors" class="text-danger mt-2"></div>
                                @endif
                            @endforeach

                            <div id="payment-fields">
                                <!-- Stripe/PayPal fields injected with JS -->
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-4">
                            <button type="submit" id="place-order" class="btn btn-primary w-100">{{ __('store.checkout.place_order') }}</button>
                        </div>
                    </form>
                </div>

                <div class="col-md-5 mt-5 mt-md-0">
                    <div class="cart-box">
                        <h3 class="cart-heading">{{ __('store.checkout.order_summary') }}</h3>

                        <div class="row border-bottom pb-2 mb-2 mt-4">
                            <div class="col-6 col-md-4">{{ __('store.checkout.subtotal') }}</div>
                            <div class="col-6 col-md-8 text-end">${{ number_format($subtotal, 2) }}</div>
                        </div>
                        <div class="row border-bottom pb-2 mb-2">
                            <div class="col-4 col-md-4">{{ __('store.checkout.shipping') }}</div>
                            <div class="col-8 col-md-8 text-end"><small>{{ __('store.checkout.shipping_info') }}</small></div>
                        </div>
                        <div class="row border-bottom pb-2 mb-2">   
                            <div class="col-6 col-md-4">{{ __('store.checkout.total') }}</div>
                            <div class="col-6 col-md-8 text-end"><span>${{ number_format($total, 2) }}</span></div>
                        </div>

                        <div class="mt-4">
                            <a href="#" class="read-more d-block text-center">{{ __('store.checkout.proceed') }}</a>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>


@endsection

@section('js')
<?php /* legacy Stripe/PayPal wiring kept for reference in git history */ ?>
@php
    $paypalReady = $paypalClientId && !str_starts_with($paypalClientId, 'your-');
    $stripeReady = $stripePublicKey && str_starts_with($stripePublicKey, 'pk_');
@endphp
@if($paypalReady)
    <script src="https://www.paypal.com/sdk/js?client-id={{ $paypalClientId }}&currency=USD"></script>
@endif
@if($stripeReady)
    <script src="https://js.stripe.com/v3/"></script>
@endif
<script>
document.addEventListener("DOMContentLoaded", function () {
    const gatewayRadios = document.querySelectorAll('input[name="gateway"]');
    const paypalContainer = document.getElementById("paypal-button-container");
    const stripeContainer = document.getElementById("card-element");
    const stripeErrors = document.getElementById("card-errors");
    const form = document.getElementById("checkout-form");
    const processUrl = "/checkout/process";
    const csrfToken = "{{ csrf_token() }}";

    const PAYPAL_READY = {{ $paypalReady ? 'true' : 'false' }};
    const STRIPE_READY = {{ $stripeReady ? 'true' : 'false' }};
    const MSG = {
        notConfigured: "{{ __('store.checkout.gateway_not_configured') }}",
        paypalInstructions: "{{ __('store.checkout.paypal_instructions') }}",
        orderFailed: "{{ __('store.checkout.order_failed') }}"
    };

    let stripe = null;
    let card = null;

    if (STRIPE_READY) {
        stripe = Stripe("{{ $stripePublicKey }}");
        card = stripe.elements().create("card");
        card.mount("#card-element");
        card.on("change", function (event) {
            if (event.error) {
                stripeErrors.textContent = event.error.message;
            } else {
                stripeErrors.textContent = "";
            }
        });
    }

    function handleResult(result) {
        if (result && result.success) {
            window.location.href = result.redirect || "/thank-you";
        } else {
            toastr.error((result && result.message) ? result.message : MSG.orderFailed);
        }
    }

    function postForm(formData) {
        fetch(processUrl, {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: formData
        })
        .then(res => res.json().then(data => ({ status: res.status, body: data })))
        .then(({ body }) => handleResult(body))
        .catch(() => toastr.error(MSG.orderFailed));
    }

    // Show correct payment fields
    gatewayRadios.forEach(radio => {
        radio.addEventListener("change", function () {
            if (this.value === "paypal") {
                paypalContainer.style.display = "block";
                stripeContainer.style.display = "none";
                stripeErrors.style.display = "none";
                if (!PAYPAL_READY) {
                    stripeErrors.textContent = MSG.notConfigured;
                    stripeErrors.style.display = "block";
                }
            } else if (this.value === "stripe") {
                stripeContainer.style.display = "block";
                paypalContainer.style.display = "none";
                stripeErrors.style.display = "block";
                stripeErrors.textContent = STRIPE_READY ? "" : MSG.notConfigured;
            } else {
                paypalContainer.style.display = "none";
                stripeContainer.style.display = "none";
                stripeErrors.style.display = "none";
            }
        });
    });

    // PayPal buttons (rendered only when the SDK loaded with a valid client id)
    if (typeof paypal !== "undefined" && PAYPAL_READY) {
        paypal.Buttons({
            createOrder: function (data, actions) {
                return actions.order.create({
                    purchase_units: [{ amount: { value: "{{ number_format($total, 2, '.', '') }}" } }]
                });
            },
            onApprove: function (data, actions) {
                return actions.order.capture().then(function (details) {
                    let formData = new FormData(form);
                    formData.append("gateway", "paypal");
                    formData.append("order_id", data.orderID);
                    formData.append("details", JSON.stringify(details));
                    postForm(formData);
                });
            }
        }).render("#paypal-button-container");
    }

    // Place order
    form.addEventListener("submit", function (e) {
        e.preventDefault();

        let selectedGateway = document.querySelector('input[name="gateway"]:checked');
        if (!selectedGateway) {
            toastr.error(MSG.orderFailed);
            return;
        }
        selectedGateway = selectedGateway.value;

        if (selectedGateway === "stripe" && STRIPE_READY) {
            stripe.createPaymentMethod({ type: "card", card: card }).then(function (result) {
                if (result.error) {
                    stripeErrors.textContent = result.error.message;
                } else {
                    let formData = new FormData(form);
                    formData.append("payment_method_id", result.paymentMethod.id);
                    postForm(formData);
                }
            });
        } else if (selectedGateway === "paypal") {
            if (PAYPAL_READY && typeof paypal !== "undefined") {
                toastr.info(MSG.paypalInstructions);
            } else {
                toastr.error(MSG.notConfigured);
            }
        } else if (selectedGateway === "stripe") {
            toastr.error(MSG.notConfigured);
        } else {
            postForm(new FormData(form));
        }
    });
});
</script>


@endsection
