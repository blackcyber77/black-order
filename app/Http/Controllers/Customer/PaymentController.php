<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Payment Gateway Controller
 * 
 * Skeleton controller for future payment gateway integration.
 * Compatible with major Indonesian PG providers:
 * - Midtrans (Snap API)
 * - Xendit (Invoice API)
 * - Doku
 * - Tripay
 * 
 * When integrating a PG, implement the methods below and
 * add the PG's SDK via composer.
 */
class PaymentController extends Controller
{
    /**
     * Create payment via payment gateway
     * 
     * Called after order is created to generate payment URL/token.
     * Replace this with actual PG SDK call.
     * 
     * Example flow (Midtrans):
     *   $snapToken = \Midtrans\Snap::getSnapToken($params);
     *   $order->update(['payment_gateway_token' => $snapToken]);
     */
    public function create(Order $order)
    {
        // Placeholder: In the future, this will call the payment gateway API
        // to create a payment transaction and return a payment URL/token.
        
        // For now, redirect to confirmation page with manual payment flow
        return redirect()->route('orders.confirmation', $order->order_number);
    }

    /**
     * Handle webhook/callback from payment gateway
     * 
     * This endpoint receives payment notifications from the PG.
     * Must be publicly accessible (no CSRF, no auth).
     * 
     * Example flow:
     *   1. Verify signature/token from PG
     *   2. Find order by gateway reference
     *   3. Update payment status
     *   4. Return 200 OK
     */
    public function callback(Request $request)
    {
        Log::info('Payment Gateway Callback', $request->all());

        try {
            // TODO: Implement based on chosen payment gateway
            // 
            // Generic flow:
            // 1. Get order reference from callback data
            // $orderNumber = $request->input('order_id'); // varies by PG
            // 
            // 2. Verify callback signature
            // $isValid = $this->verifySignature($request);
            // 
            // 3. Find and update order
            // $order = Order::where('order_number', $orderNumber)->first();
            // if ($order && $isValid) {
            //     $status = $request->input('transaction_status'); // varies by PG
            //     if (in_array($status, ['capture', 'settlement'])) {
            //         $order->markAsPaid($request->input('transaction_id'));
            //     }
            //     $order->transaction->update([
            //         'gateway_response' => $request->all(),
            //     ]);
            // }

            return response()->json(['status' => 'ok'], 200);

        } catch (\Exception $e) {
            Log::error('Payment callback error: ' . $e->getMessage());
            return response()->json(['status' => 'error'], 500);
        }
    }

    /**
     * Handle payment notification (alternative endpoint)
     * Some PGs use separate notification endpoints.
     */
    public function notification(Request $request)
    {
        Log::info('Payment Gateway Notification', $request->all());

        // Same logic as callback, implement based on PG
        return response()->json(['status' => 'ok'], 200);
    }

    /**
     * Check payment status
     * Customer can check if their payment has been processed.
     */
    public function checkStatus(Order $order)
    {
        // TODO: When PG is integrated, call PG API to get real-time status
        // For now, return current DB status
        
        return response()->json([
            'order_number' => $order->order_number,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'is_expired' => $order->isPaymentExpired(),
            'paid_at' => $order->paid_at?->format('d M Y H:i'),
        ]);
    }

    /**
     * Verify callback signature from payment gateway
     * Implement based on chosen PG provider.
     */
    // private function verifySignature(Request $request): bool
    // {
    //     // Midtrans example:
    //     // $serverKey = config('services.midtrans.server_key');
    //     // $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
    //     // return $hashed === $request->signature_key;
    //     
    //     return false;
    // }
}
