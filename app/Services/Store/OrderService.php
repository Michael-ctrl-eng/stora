<?php

namespace App\Services\Store;

use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\Auth;

class OrderService
{
    /**
     * Create a new order from a completed PayPal client-side capture.
     *
     * @param  array  $paypalResult  The PayPal API capture result
     */
    public function createOrderFromPaypal(array $paypalResult): Order
    {
        $payer = $paypalResult['payer'] ?? [];
        $purchaseUnit = $paypalResult['purchase_units'][0] ?? [];
        $amount = $purchaseUnit['payments']['captures'][0]['amount']['value'] ?? 0;

        return $this->createOrder(
            (float) $amount,
            $payer['email_address'] ?? null,
            'completed'
        );
    }

    /**
     * Create a new order from a succeeded Stripe PaymentIntent.
     */
    public function createOrderFromStripe($paymentIntent, ?string $email = null): Order
    {
        return $this->createOrder(
            $paymentIntent->amount / 100,
            $email,
            'completed'
        );
    }

    /**
     * Persist an order, its line items and shipping address from the
     * session cart. Used by every payment gateway flow.
     */
    public function createOrder(float $amount, ?string $email = null, string $status = 'pending'): Order
    {
        $customerId = Auth::guard('customer')->check() ? Auth::guard('customer')->id() : null;

        $order = Order::create([
            'customer_id' => $customerId,
            'guest_email' => $email ?? session('checkout_email'),
            'total_amount' => $amount,
            'status' => $status,
        ]);

        $cart = session('cart', []);

        foreach ($cart as $item) {
            OrderDetail::create([
                'order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        $shipping = session('checkout_shipping');

        if ($shipping) {
            ShippingAddress::create([
                'order_id' => $order->id,
                'customer_id' => $customerId,
                'name' => $shipping['name'],
                'phone' => $shipping['phone'],
                'address' => $shipping['address'],
                'city' => $shipping['city'],
                'postal_code' => $shipping['postal_code'],
                'country' => $shipping['country'],
            ]);
        }

        session()->forget(['cart', 'cart_count', 'checkout_shipping', 'checkout_email']);

        return $order;
    }
}
