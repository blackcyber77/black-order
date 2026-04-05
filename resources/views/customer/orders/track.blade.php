@extends('layouts.customer')

@section('title', 'Lacak Pesanan')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-navy-900">Lacak Pesanan</h1>
    </div>

    <!-- Search Form -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="{{ route('orders.track') }}" method="GET" class="relative">
            <input type="text" name="order_number" value="{{ request('order_number') }}" placeholder="Masukkan Nomor Pesanan (ORD-...)" 
                class="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition font-mono uppercase">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <button type="submit" class="absolute right-2 top-1/2 -translate-y-1/2 bg-navy-900 text-white px-4 py-1.5 rounded-lg text-sm font-bold hover:bg-navy-800 transition">
                Cari
            </button>
        </form>
    </div>

    @if(isset($order))
    <div class="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden relative">
        <!-- Header Status -->
        <div class="bg-navy-900 p-6 text-white text-center relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-white/10 rounded-full blur-2xl -mr-10 -mt-10"></div>
            <p class="text-xs text-slate-400 uppercase tracking-widest mb-1">Status Pesanan</p>
            <h2 class="text-2xl font-bold mb-2">{{ $order->status_label }}</h2>
            <p class="font-mono text-sm opacity-70">{{ $order->order_number }}</p>
        </div>

        <!-- Timeline -->
        <div class="p-8">
            <div class="relative pl-4 border-l-2 border-gray-100 space-y-8">
                <!-- Step 1: Pending -->
                <div class="relative">
                    <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full flex items-center justify-center border-4 border-white 
                        {{ in_array($order->status, ['pending', 'confirmed', 'processing', 'delivering', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas fa-clipboard-check text-xs"></i>
                    </div>
                    <div class="pl-4">
                        <h4 class="font-bold text-navy-900 text-sm">Pesanan Dibuat</h4>
                        <p class="text-xs text-gray-500">{{ $order->created_at->format('d M H:i') }}</p>
                    </div>
                </div>

                <!-- Step 2: Confirmed/Processing -->
                <div class="relative">
                    <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full flex items-center justify-center border-4 border-white 
                        {{ in_array($order->status, ['confirmed', 'processing', 'delivering', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas fa-utensils text-xs"></i>
                    </div>
                    <div class="pl-4">
                        <h4 class="font-bold text-navy-900 text-sm">Sedang Disiapkan</h4>
                        <p class="text-xs text-gray-500">Tenant sedang menyiapkan pesanan</p>
                    </div>
                </div>

                <!-- Step 3: Delivering -->
                <div class="relative">
                    <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full flex items-center justify-center border-4 border-white 
                        {{ in_array($order->status, ['delivering', 'completed']) ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas fa-motorcycle text-xs"></i>
                    </div>
                    <div class="pl-4">
                        <h4 class="font-bold text-navy-900 text-sm">Dalam Pengantaran</h4>
                        <p class="text-xs text-gray-500">Pesanan menuju lokasi Anda</p>
                    </div>
                </div>

                <!-- Step 4: Completed -->
                <div class="relative">
                    <div class="absolute -left-[21px] top-0 w-10 h-10 rounded-full flex items-center justify-center border-4 border-white 
                        {{ $order->status == 'completed' ? 'bg-green-500 text-white' : 'bg-gray-200 text-gray-400' }}">
                        <i class="fas fa-smile text-xs"></i>
                    </div>
                    <div class="pl-4">
                        <h4 class="font-bold text-navy-900 text-sm">Selesai</h4>
                        <p class="text-xs text-gray-500">Pesanan telah diterima</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Details Toggle -->
        <div class="bg-gray-50 p-4 border-t border-gray-100 text-center">
             <button onclick="document.getElementById('orderDetails').classList.toggle('hidden')" class="text-sm font-bold text-navy-900 hover:text-orange-600 transition flex items-center justify-center gap-2 w-full">
                Lihat Detail Pesanan <i class="fas fa-chevron-down"></i>
            </button>
            <div id="orderDetails" class="hidden mt-4 text-left border-t border-gray-200 pt-4 animate-fade-in-down">
                <div class="space-y-3">
                    @foreach($order->items as $item)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ $item->quantity }}x {{ $item->menuItem->name }}</span>
                        <span class="font-medium">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="border-t border-gray-200 mt-3 pt-3 flex justify-between font-bold text-navy-900">
                    <span>Total</span>
                    <span>{{ $order->formatted_total }}</span>
                </div>
            </div>
        </div>
    </div>
    @elseif(request('order'))
    <div class="text-center py-12">
        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <i class="fas fa-search text-red-400 text-xl"></i>
        </div>
        <h3 class="font-bold text-navy-900">Pesanan Tidak Ditemukan</h3>
        <p class="text-sm text-gray-500 mt-1">Pastikan nomor pesanan yang Anda masukkan benar.</p>
    </div>
    @endif
</div>
@endsection
