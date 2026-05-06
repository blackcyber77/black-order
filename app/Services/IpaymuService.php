<?php

namespace App\Services;

use App\Models\Order;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class IpaymuService
{
    public function createRedirectPayment(Order $order): array
    {
        $order->loadMissing('items.menuItem');

        $payload = [
            'account' => $this->va(),
            'product' => $order->items->map(fn ($item) => $item->menuItem?->name ?? 'Menu')->values()->all(),
            'qty' => $order->items->map(fn ($item) => (int) $item->quantity)->values()->all(),
            'price' => $order->items->map(fn ($item) => (int) round((float) $item->price))->values()->all(),
            'description' => $order->items->map(fn ($item) => 'Order ' . $order->order_number)->values()->all(),
            // Payment Redirect: prefill buyer details so user doesn't retype on iPaymu page.
            // iPaymu API v2 (sandbox): use buyerName/buyerEmail/buyerPhone.
            'buyerName' => (string) $order->customer_name,
            'buyerEmail' => (string) ($order->customer_email ?: 'customer+' . strtolower($order->order_number) . '@example.local'),
            'buyerPhone' => (string) ($order->customer_phone ?: '081000000000'),
            'notifyUrl' => route('payment.callback'),
            'returnUrl' => route('orders.confirmation', $order->order_number),
            'cancelUrl' => route('orders.confirmation', $order->order_number),
            'referenceId' => $order->order_number,
            'expired' => (int) config('services.ipaymu.expired', 24),
            'expiredType' => config('services.ipaymu.expired_type', 'hours'),
        ];

        $paymentMethod = config('services.ipaymu.payment_method', 'qris');
        $paymentChannel = config('services.ipaymu.payment_channel', 'qris');
        if (!empty($paymentMethod)) {
            $payload['paymentMethod'] = $paymentMethod;
        }
        if (!empty($paymentChannel)) {
            $payload['paymentChannel'] = $paymentChannel;
        }

        $response = $this->post('payment', $payload);
        $data = Arr::get($response, 'Data', Arr::get($response, 'data', []));

        $paymentUrl = Arr::get($data, 'Url')
            ?? Arr::get($data, 'url')
            ?? Arr::get($data, 'PaymentUrl')
            ?? Arr::get($data, 'paymentUrl');

        if (empty($paymentUrl)) {
            throw new RuntimeException('Gagal membuat URL pembayaran iPaymu.');
        }

        return [
            'url' => $paymentUrl,
            'session_id' => Arr::get($data, 'SessionID')
                ?? Arr::get($data, 'sessionID')
                ?? Arr::get($data, 'sessionId'),
            'transaction_id' => Arr::get($data, 'TransactionId')
                ?? Arr::get($data, 'transactionId')
                ?? Arr::get($data, 'TrxId')
                ?? Arr::get($data, 'trx_id'),
            'expired_at' => $this->parseExpiry(
                Arr::get($data, 'Expired')
                    ?? Arr::get($data, 'expired')
                    ?? Arr::get($data, 'ExpiredAt')
                    ?? Arr::get($data, 'expired_at')
            ),
            'raw' => $response,
        ];
    }

    public function checkTransaction(string $transactionId): array
    {
        return $this->post('transaction', [
            'transactionId' => $transactionId,
        ]);
    }

    private function post(string $path, array $payload): array
    {
        $body = json_encode($payload, JSON_UNESCAPED_SLASHES);
        if ($body === false) {
            throw new RuntimeException('Payload iPaymu tidak valid.');
        }

        $requestBody = strtolower(hash('sha256', $body));
        $stringToSign = 'POST:' . $this->va() . ':' . $requestBody . ':' . $this->apiKey();
        $signature = hash_hmac('sha256', $stringToSign, $this->apiKey());
        $timestamp = now()->format('YmdHis');

        $url = rtrim((string) config('services.ipaymu.base_url'), '/') . '/' . ltrim($path, '/');

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'va' => $this->va(),
            'signature' => $signature,
            'timestamp' => $timestamp,
        ])
            ->timeout(30)
            ->withBody($body, 'application/json')
            ->post($url);

        if (!$response->successful()) {
            throw new RuntimeException('iPaymu HTTP error ' . $response->status() . ': ' . $response->body());
        }

        $decoded = $response->json();
        if (!is_array($decoded)) {
            throw new RuntimeException('Response iPaymu tidak valid.');
        }

        $status = (string) (Arr::get($decoded, 'Status', Arr::get($decoded, 'status', '')));
        $isSuccess = in_array(strtolower($status), ['200', '201', 'success', 'ok'], true);
        if (!$isSuccess && $status !== '') {
            $message = Arr::get($decoded, 'Message', Arr::get($decoded, 'message', 'Unknown error'));
            throw new RuntimeException('iPaymu error ' . $status . ': ' . $message);
        }

        return $decoded;
    }

    private function parseExpiry(?string $value): ?Carbon
    {
        if (empty($value)) {
            return null;
        }

        try {
            return Carbon::parse($value);
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function va(): string
    {
        $va = (string) config('services.ipaymu.va');
        if ($va === '') {
            throw new RuntimeException('IPAYMU_VA belum diatur.');
        }
        return $va;
    }

    private function apiKey(): string
    {
        $key = (string) config('services.ipaymu.api_key');
        if ($key === '') {
            throw new RuntimeException('IPAYMU_API_KEY belum diatur.');
        }
        return $key;
    }
}
