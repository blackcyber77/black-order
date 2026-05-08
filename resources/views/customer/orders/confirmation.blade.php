@extends('layouts.customer')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12 max-w-lg w-full text-center relative overflow-hidden">

        {{-- Top Gradient --}}
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-orange-500 via-orange-400 to-yellow-400"></div>

        {{-- Session Alerts --}}
        @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-exclamation-circle flex-shrink-0"></i>
            <span>{{ session('error') }}</span>
        </div>
        @endif
        @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl text-sm flex items-center gap-2">
            <i class="fas fa-check-circle flex-shrink-0"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        {{-- Icon --}}
        <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-check-circle text-5xl text-green-500"></i>
        </div>

        <h1 class="text-3xl font-bold text-navy-900 mb-2">Pesanan Diterima!</h1>
        <p class="text-gray-500 mb-8">Terima kasih <strong>{{ $order->customer_name }}</strong>, pesanan Anda sedang kami proses.</p>

        {{-- Order Number --}}
        <div class="bg-gray-50 rounded-2xl p-6 mb-5 border border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-bold mb-1">Nomor Pesanan</p>
            <p class="text-2xl font-mono font-bold text-navy-900 tracking-wider mb-4">{{ $order->order_number }}</p>

            <div class="flex justify-between items-center text-sm border-t border-gray-200 pt-4">
                <span class="text-gray-500">Total Pembayaran</span>
                <span class="font-bold text-orange-600">{{ $order->formatted_total }}</span>
            </div>
        </div>

        {{-- Payment Status Card --}}
        @php
            $isPaid = in_array($order->payment_status, ['paid', 'verified']);
            $isPending = $order->payment_status === 'pending';
            $isFailed = in_array($order->payment_status, ['failed', 'expired']);
        @endphp

        <div class="rounded-2xl p-5 mb-5 border
            @if($isPaid) bg-green-50 border-green-100
            @elseif($isFailed) bg-red-50 border-red-100
            @else bg-yellow-50 border-yellow-100
            @endif
        ">
            {{-- Status Header --}}
            <div class="flex items-center justify-center gap-3 mb-4">
                @if($isPaid)
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <span class="font-bold text-green-800">Pembayaran Diterima</span>
                @elseif($isFailed)
                    <i class="fas fa-times-circle text-red-500 text-xl"></i>
                    <span class="font-bold text-red-800">Pembayaran Gagal / Kadaluarsa</span>
                @else
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    <span class="font-bold text-yellow-800">Menunggu Pembayaran</span>
                @endif
            </div>

            {{-- Status Details --}}
            <div class="text-sm space-y-2 text-left">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode</span>
                    <span class="font-semibold">{{ $order->payment_method_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-semibold
                        @if($isPaid) text-green-700
                        @elseif($isFailed) text-red-700
                        @else text-yellow-700
                        @endif
                    ">{{ $order->payment_status_label }}</span>
                </div>
                @if($isPaid && $order->paid_at)
                <div class="flex justify-between">
                    <span class="text-gray-600">Dibayar pada</span>
                    <span class="font-semibold text-green-700">{{ $order->paid_at->format('d M Y, H:i') }}</span>
                </div>
                @endif
            </div>

            {{-- Pending: show gateway URL + expiry --}}
            @if($isPending)
            <div class="mt-4 pt-4 border-t border-yellow-200 space-y-3">

                @if($order->payment_expiry && !$order->isPaymentExpired())
                <div class="flex items-center justify-center gap-2 text-xs text-yellow-800 bg-yellow-100 rounded-lg py-2 px-3">
                    <i class="fas fa-hourglass-half"></i>
                    <span>Selesaikan pembayaran sebelum
                        <strong>{{ $order->payment_expiry->format('d M Y, H:i') }}</strong>
                    </span>
                </div>
                @elseif($order->isPaymentExpired())
                <div class="flex items-center justify-center gap-2 text-xs text-red-800 bg-red-50 rounded-lg py-2 px-3">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Link pembayaran sudah <strong>kadaluarsa</strong>. Hubungi kasir.</span>
                </div>
                @endif

                @if(!$order->isPaymentExpired())
                <a
                    href="{{ $order->payment_gateway_url ?: route('payment.create', $order) }}"
                    id="payBtn"
                    class="flex items-center justify-center gap-2 w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white rounded-xl font-bold text-sm shadow-lg shadow-orange-500/30 hover:-translate-y-0.5 transition-all duration-200"
                >
                    <i class="fas fa-credit-card"></i>
                    Bayar Sekarang via iPaymu
                </a>
                <p class="text-xs text-yellow-700 text-center">
                    <i class="fas fa-info-circle mr-1"></i>
                    Data Anda (nama, email, HP) sudah terisi otomatis di halaman iPaymu
                </p>
                @endif
            </div>
            @endif
        </div>

        {{-- Navigation Buttons --}}
        <div class="space-y-3">
            <a href="{{ route('orders.track') }}?order_number={{ $order->order_number }}"
               class="block w-full py-3.5 bg-navy-900 text-white rounded-xl font-bold hover:bg-navy-800 transition shadow-lg shadow-navy-900/20">
                <i class="fas fa-search mr-2"></i>Lacak Pesanan
            </a>
            <a href="{{ route('home') }}"
               class="block w-full py-3.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition">
                Kembali ke Menu
            </a>
        </div>

        <p class="text-xs text-gray-400 mt-8">
            Simpan nomor pesanan Anda untuk mengecek status sewaktu-waktu.
        </p>
    </div>
</div>
@endsection
