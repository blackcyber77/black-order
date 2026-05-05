<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\IpaymuService;
use Illuminate\Http\JsonResponse;
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
    public function create(Order $order, IpaymuService $ipaymu)
    {
        if ($order->payment_status === 'paid' || $order->payment_status === 'verified') {
            return redirect()->route('orders.confirmation', $order->order_number)
                ->with('success', 'Pesanan sudah dibayar.');
        }

        try {
            $payment = $ipaymu->createRedirectPayment($order);
            $gatewayReference = $payment['session_id'] ?: ($payment['transaction_id'] ?: $order->payment_gateway_ref);

            $order->update([
                'payment_gateway' => 'ipaymu',
                'payment_gateway_ref' => $gatewayReference,
                'payment_gateway_url' => $payment['url'],
                'payment_gateway_token' => $payment['session_id'],
                'payment_expiry' => $payment['expired_at'],
            ]);

            if ($order->transaction) {
                $order->transaction->update([
                    'gateway_reference' => $gatewayReference,
                    'gateway_response' => $payment['raw'],
                ]);
            }

            return redirect()->away($payment['url']);
        } catch (\Throwable $e) {
            Log::error('iPaymu create payment error', [
                'order_number' => $order->order_number,
                'error' => $e->getMessage(),
            ]);

            return redirect()->route('orders.confirmation', $order->order_number)
                ->with('error', 'Gagal membuat pembayaran iPaymu: ' . $e->getMessage());
        }
    }

    public function callback(Request $request): JsonResponse
    {
        return $this->handleGatewayCallback($request, 'callback');
    }

    public function notification(Request $request): JsonResponse
    {
        return $this->handleGatewayCallback($request, 'notification');
    }

    public function checkStatus(Order $order, IpaymuService $ipaymu)
    {
        $gatewayStatus = null;

        if (!empty($order->payment_gateway_ref)) {
            try {
                $gatewayStatus = $ipaymu->checkTransaction($order->payment_gateway_ref);
            } catch (\Throwable $e) {
                Log::warning('iPaymu check status failed', [
                    'order_number' => $order->order_number,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return response()->json([
            'order_number' => $order->order_number,
            'payment_method' => $order->payment_method,
            'payment_status' => $order->payment_status,
            'payment_status_label' => $order->payment_status_label,
            'is_expired' => $order->isPaymentExpired(),
            'paid_at' => $order->paid_at?->format('d M Y H:i'),
            'gateway_status' => $gatewayStatus,
        ]);
    }

    private function handleGatewayCallback(Request $request, string $source): JsonResponse
    {
        $payload = $request->all();
        Log::info('iPaymu ' . $source . ' payload', $payload);

        try {
            $referenceId = (string) (
                $request->input('referenceId')
                ?? $request->input('reference_id')
                ?? $request->input('merchant_ref')
                ?? $request->input('reference')
                ?? ''
            );

            $gatewayReference = (string) (
                $request->input('transactionId')
                ?? $request->input('trx_id')
                ?? $request->input('trxId')
                ?? $request->input('sid')
                ?? $request->input('sessionId')
                ?? $request->input('SessionID')
                ?? ''
            );

            $statusRaw = strtolower((string) (
                $request->input('status')
                ?? $request->input('Status')
                ?? $request->input('transactionStatus')
                ?? $request->input('payment_status')
                ?? ''
            ));

            $order = null;
            if ($referenceId !== '') {
                $order = Order::where('order_number', $referenceId)->first();
            }
            if (!$order && $gatewayReference !== '') {
                $order = Order::where('payment_gateway_ref', $gatewayReference)->first();
            }

            if (!$order) {
                Log::warning('iPaymu callback order not found', [
                    'reference_id' => $referenceId,
                    'gateway_reference' => $gatewayReference,
                ]);
                return response()->json(['status' => 'accepted'], 200);
            }

            $isPaid = in_array($statusRaw, [
                'berhasil', 'success', 'paid', 'settlement', 'capture', 'completed', 'sukses', 'lunas',
            ], true);
            $isFailed = in_array($statusRaw, [
                'failed', 'expire', 'expired', 'cancelled', 'canceled', 'denied', 'gagal',
            ], true);

            $order->update([
                'payment_gateway' => 'ipaymu',
                'payment_gateway_ref' => $gatewayReference ?: $order->payment_gateway_ref,
            ]);

            if ($order->transaction) {
                $order->transaction->update([
                    'gateway_reference' => $gatewayReference ?: $order->transaction->gateway_reference,
                    'gateway_response' => $payload,
                ]);
            }

            if ($isPaid) {
                $order->markAsPaid($gatewayReference ?: null);
                if ($order->status === 'pending') {
                    $order->update(['status' => 'confirmed']);
                }
            } elseif ($isFailed) {
                $order->update(['payment_status' => 'failed']);
                if ($order->transaction) {
                    $order->transaction->update(['payment_status' => 'failed']);
                }
            }

            return response()->json(['status' => 'ok'], 200);
        } catch (\Throwable $e) {
            Log::error('iPaymu callback error', [
                'source' => $source,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['status' => 'error'], 500);
        }
    }

}
