<?php

namespace App\Http\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Models\PaymentGateway;
use App\Models\ProductVariant;
use App\Services\PaymentGateway\PaymentManager;
use App\Services\Store\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function index()
    {
        $paymentGateways = PaymentGateway::with('configs')
            ->where('is_active', 1)
            ->get();

        $paypal = $paymentGateways->firstWhere('code', 'paypal');
        $paypalClientId = $paypal
            ? $paypal->getConfigValue('client_id')
            : null;

        $stripe = $paymentGateways->firstWhere('code', 'stripe');
        $stripePublicKey = $stripe
            ? $stripe->getConfigValue('public_key')
            : null;

        $cart = Session::get('cart', []);
        $subtotal = 0;

        foreach ($cart as $key => $item) {
            $product = \App\Models\Product::with(['translations', 'thumbnail'])->find($item['product_id']);

            $variant = isset($item['variant_id'])
                ? ProductVariant::with('images')->find($item['variant_id'])
                : ProductVariant::where('product_id', $item['product_id'])->where('is_primary', true)->first();

            $subtotal += $item['price'] * $item['quantity'];
        }

        $shipping = null;
        $total = $subtotal + ($shipping ?? 0);

        return view('themes.xylo.checkout', compact('cart', 'subtotal', 'shipping', 'total', 'paymentGateways', 'paypalClientId', 'stripePublicKey'));
    }

    public function process(Request $request)
    {
        $gatewayCode = (string) $request->input('gateway', '');

        // Persist the shipping details submitted with the checkout form so
        // they can be attached to the order once payment completes.
        if ($request->filled('address') && $request->filled('email')) {
            Session::put('checkout_shipping', [
                'name' => trim($request->input('first_name', '').' '.$request->input('last_name', '')),
                'phone' => (string) $request->input('phone', ''),
                'address' => trim($request->input('address', '').($request->filled('suite') ? ', '.$request->input('suite') : '')),
                'city' => (string) $request->input('city', ''),
                'postal_code' => (string) $request->input('zipcode', ''),
                'country' => (string) $request->input('country', ''),
            ]);
            Session::put('checkout_email', (string) $request->input('email'));
        }

        $cart = Session::get('cart', []);

        if (empty($cart)) {
            return response()->json([
                'success' => false,
                'message' => __('store.checkout.cart_empty'),
            ], 422);
        }

        try {
            // PayPal: the client-side SDK captures the payment and posts the
            // capture result back to us.
            if ($gatewayCode === 'paypal') {
                $details = $request->input('details');
                if (is_string($details)) {
                    $details = json_decode($details, true);
                }

                if (is_array($details) && (($details['status'] ?? '') === 'COMPLETED')) {
                    $order = app(OrderService::class)->createOrderFromPaypal($details);

                    return response()->json([
                        'success' => true,
                        'order_id' => $order->id,
                        'redirect' => url('/thank-you'),
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => __('store.checkout.payment_not_completed'),
                ], 422);
            }

            // Stripe: the client-side Elements flow creates a payment method
            // which we confirm server-side against a PaymentIntent.
            if ($gatewayCode === 'stripe' && $request->filled('payment_method_id')) {
                $service = PaymentManager::make('stripe');

                $total = collect($cart)->sum(fn ($item) => $item['price'] * $item['quantity']);

                $intent = $service->createPaymentIntent((int) round($total * 100), 'usd');
                $confirmed = $service->confirmPaymentIntent($intent->id, (string) $request->input('payment_method_id'));

                if (in_array($confirmed->status, ['succeeded', 'requires_capture'])) {
                    $order = app(OrderService::class)->createOrderFromStripe(
                        $confirmed,
                        (string) $request->input('email')
                    );

                    return response()->json([
                        'success' => true,
                        'order_id' => $order->id,
                        'redirect' => url('/thank-you'),
                    ]);
                }

                return response()->json([
                    'success' => false,
                    'message' => __('store.checkout.payment_not_completed'),
                ], 422);
            }

            return response()->json([
                'success' => false,
                'message' => __('store.checkout.gateway_unsupported', ['gateway' => $gatewayCode]),
            ], 422);
        } catch (\Exception $e) {
            Log::error('Checkout process failed: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Order confirmation page shown after a successful payment.
     */
    public function thankYou()
    {
        return view('themes.xylo.thank-you');
    }

    /**
     * PayPal success callback (server-side capture fallback).
     */
    public function paypalSuccess(Request $request, OrderService $orderService)
    {
        $orderId = $request->query('token'); // PayPal returns ?token=ORDER_ID

        try {
            $paypal = PaymentManager::make('paypal');
            $result = $paypal->captureOrder($orderId);

            if (($result['status'] ?? null) === 'COMPLETED') {

                $order = $orderService->createOrderFromPaypal($result);

                return response()->json([
                    'success' => true,
                    'message' => 'Payment completed & order stored successfully.',
                    'order_id' => $order->id,
                    'details' => $result,
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not completed.',
                'details' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('PayPal success error: '.$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * PayPal cancel callback.
     */
    public function paypalCancel()
    {
        return response()->json([
            'success' => false,
            'message' => 'Payment was cancelled by user.',
        ]);
    }
}
