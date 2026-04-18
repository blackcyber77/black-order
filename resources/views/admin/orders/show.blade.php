@extends('layouts.admin')

@section('title', 'Detail Pesanan')

@section('content')
<div class="max-w-3xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-navy">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h2 class="text-lg font-semibold text-navy">Detail Pesanan #{{ $order->order_number }}</h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-navy mb-4">Informasi Pesanan</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="px-2 py-1 text-xs rounded-full
                        @switch($order->status)
                            @case('pending') bg-yellow-100 text-yellow-800 @break
                            @case('completed') bg-green-100 text-green-800 @break
                            @case('cancelled') bg-red-100 text-red-800 @break
                            @default bg-blue-100 text-blue-800
                        @endswitch
                    ">{{ $order->status_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Pembayaran</span>
                    <span class="font-semibold">{{ $order->payment_method_label }} — {{ $order->payment_status_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Waktu</span>
                    <span>{{ $order->created_at->format('d M Y H:i') }}</span>
                </div>
                @if($order->held_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Ditahan</span>
                    <span class="text-amber-600 font-medium">{{ $order->held_at->format('d M Y H:i') }}</span>
                </div>
                @endif
                @if($order->paid_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibayar</span>
                    <span class="text-green-600 font-medium">{{ $order->paid_at->format('d M Y H:i') }}</span>
                </div>
                @endif
            </div>
        </div>

        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-navy mb-4">Pelanggan</h3>
            <div class="space-y-3 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Nama</span>
                    <span>{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Telepon</span>
                    <span>{{ $order->customer_phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Lokasi</span>
                    <span>{{ $order->full_location }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Kasir</span>
                    <span>{{ $order->cashier?->name ?? '-' }}</span>
                </div>
            </div>
        </div>
    </div>

    @if($order->voided_at || $order->void_reason)
    <div class="bg-red-50 border border-red-100 text-red-700 rounded-xl p-4 mb-6">
        <h3 class="font-semibold mb-2"><i class="fas fa-ban mr-2"></i>Informasi Void</h3>
        <p class="text-sm"><strong>Waktu:</strong> {{ $order->voided_at?->format('d M Y H:i') ?? '-' }}</p>
        <p class="text-sm mt-1"><strong>Alasan:</strong> {{ $order->void_reason ?? '-' }}</p>
    </div>
    @endif

    <!-- Payment Gateway Info (if available) -->
    @if($order->payment_gateway || $order->payment_gateway_ref)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-navy mb-4">
            <i class="fas fa-credit-card mr-2 text-blue-500"></i>
            Info Payment Gateway
        </h3>
        <div class="space-y-3 text-sm">
            @if($order->payment_gateway)
            <div class="flex justify-between">
                <span class="text-gray-600">Provider</span>
                <span class="font-semibold uppercase">{{ $order->payment_gateway }}</span>
            </div>
            @endif
            @if($order->payment_gateway_ref)
            <div class="flex justify-between">
                <span class="text-gray-600">Reference ID</span>
                <span class="font-mono text-xs bg-gray-100 px-2 py-1 rounded">{{ $order->payment_gateway_ref }}</span>
            </div>
            @endif
            @if($order->payment_expiry)
            <div class="flex justify-between">
                <span class="text-gray-600">Batas Waktu</span>
                <span class="{{ $order->isPaymentExpired() ? 'text-red-600' : '' }}">
                    {{ $order->payment_expiry->format('d M Y H:i') }}
                    @if($order->isPaymentExpired())
                        <span class="text-xs text-red-500">(Kadaluarsa)</span>
                    @endif
                </span>
            </div>
            @endif
        </div>
    </div>
    @endif

    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-navy mb-4">Item Pesanan</h3>
        @foreach($order->items as $item)
        <div class="flex justify-between py-2 border-b text-sm">
            <span>{{ $item->menuItem->name }} x{{ $item->quantity }}</span>
            <span class="text-orange">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
        </div>
        @endforeach
        <div class="mt-4 pt-4 border-t space-y-2 text-sm">
            <div class="flex justify-between"><span>Subtotal</span><span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Biaya Layanan</span><span>Rp {{ number_format($order->service_fee, 0, ',', '.') }}</span></div>
            <div class="flex justify-between"><span>Biaya Pengiriman</span><span>Rp {{ number_format($order->delivery_fee, 0, ',', '.') }}</span></div>
            <div class="flex justify-between font-bold text-lg text-navy pt-2 border-t"><span>Total</span><span class="text-orange">{{ $order->formatted_total }}</span></div>
        </div>
    </div>

    @if($order->payment_proof)
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-navy mb-4">Bukti Pembayaran</h3>
        <img src="{{ asset('storage/' . $order->payment_proof) }}" alt="Bukti Pembayaran" class="max-w-sm rounded-lg border">
    </div>
    @endif

    <!-- Actions -->
    <div class="bg-white rounded-xl shadow p-6 mb-6">
        <h3 class="font-semibold text-navy mb-4">Aksi</h3>
        <div class="flex flex-wrap gap-3">
            <a href="{{ route('admin.orders.thermal-print', ['order' => $order->id, 'autoprint' => 1]) }}" target="_blank" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm font-semibold hover:bg-navy-800 transition">
                <i class="fas fa-print mr-1"></i> Print Nota Thermal
            </a>

            {{-- Verify Payment --}}
            @if($order->isCashless() && $order->payment_status !== 'verified')
            <form action="{{ route('admin.orders.verify', $order) }}" method="POST">
                @csrf
                @method('PATCH')
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold hover:bg-green-700 transition">
                    <i class="fas fa-check mr-1"></i> Verifikasi Pembayaran
                </button>
            </form>
            @endif

            {{-- Update Status --}}
            <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="flex items-center gap-2">
                @csrf
                @method('PATCH')
                <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                    <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="confirmed" {{ $order->status == 'confirmed' ? 'selected' : '' }}>Dikonfirmasi</option>
                    <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Diproses</option>
                    <option value="delivering" {{ $order->status == 'delivering' ? 'selected' : '' }}>Diantar</option>
                    <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Selesai</option>
                    <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm font-semibold hover:bg-navy-800 transition">
                    Update Status
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
