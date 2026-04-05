@extends('layouts.customer')

@section('title', 'Pesanan Berhasil')

@section('content')
<div class="min-h-[80vh] flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 p-8 md:p-12 max-w-lg w-full text-center relative overflow-hidden">
        <!-- Top Gradient -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-orange-500 via-orange-400 to-yellow-400"></div>
        
        <div class="w-24 h-24 bg-green-50 rounded-full flex items-center justify-center mx-auto mb-6 animate-bounce">
            <i class="fas fa-check-circle text-5xl text-green-500"></i>
        </div>

        <h1 class="text-3xl font-bold text-navy-900 mb-2">Pesanan Diterima!</h1>
        <p class="text-gray-500 mb-8">Terima kasih, pesanan Anda sedang kami proses.</p>

        <div class="bg-gray-50 rounded-2xl p-6 mb-6 border border-gray-100">
            <p class="text-xs text-gray-400 uppercase tracking-wide font-bold mb-1">Nomor Pesanan</p>
            <p class="text-2xl font-mono font-bold text-navy-900 tracking-wider mb-4">{{ $order->order_number }}</p>
            
            <div class="flex justify-between items-center text-sm border-t border-gray-200 pt-4">
                <span class="text-gray-500">Total Pembayaran</span>
                <span class="font-bold text-orange-600">{{ $order->formatted_total }}</span>
            </div>
        </div>

        <!-- Payment Status Card -->
        <div class="rounded-2xl p-5 mb-6 border 
            @if($order->payment_status === 'paid' || $order->payment_status === 'verified')
                bg-green-50 border-green-100
            @else
                bg-yellow-50 border-yellow-100
            @endif
        ">
            <div class="flex items-center justify-center gap-3 mb-3">
                @if($order->payment_status === 'paid' || $order->payment_status === 'verified')
                    <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    <span class="font-bold text-green-800">Pembayaran Diterima</span>
                @else
                    <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    <span class="font-bold text-yellow-800">Menunggu Verifikasi Pembayaran</span>
                @endif
            </div>

            <div class="text-sm space-y-2">
                <div class="flex justify-between">
                    <span class="text-gray-600">Metode</span>
                    <span class="font-semibold">{{ $order->payment_method_label }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span class="font-semibold 
                        @if($order->payment_status === 'paid' || $order->payment_status === 'verified') text-green-700
                        @else text-yellow-700
                        @endif
                    ">{{ $order->payment_status_label }}</span>
                </div>
            </div>

            @if($order->payment_status === 'pending' && $order->payment_method === 'qris')
            <div class="mt-4 pt-4 border-t border-yellow-200">
                <p class="text-xs text-yellow-800 mb-3">Belum bayar? Scan QRIS di bawah:</p>
                @if($qrisImage)
                    <div class="bg-white p-2 rounded-xl shadow-sm border border-gray-100 inline-block">
                        <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS" class="h-32 object-contain">
                    </div>
                @endif
            </div>
            @endif
        </div>

        <div class="space-y-3">
            <a href="{{ route('orders.track') }}?order_number={{ $order->order_number }}" class="block w-full py-3.5 bg-navy-900 text-white rounded-xl font-bold hover:bg-navy-800 transition shadow-lg shadow-navy-900/20">
                Lacak Pesanan
            </a>
            <a href="{{ route('home') }}" class="block w-full py-3.5 bg-white border border-gray-200 text-gray-600 rounded-xl font-bold hover:bg-gray-50 transition">
                Kembali ke Menu
            </a>
        </div>
        
        <p class="text-xs text-gray-400 mt-8">
            Simpan nomor pesanan Anda untuk mengecek status sewaktu-waktu.
        </p>
    </div>
</div>
@endsection
