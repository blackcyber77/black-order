@extends('layouts.customer')

@section('title', 'Keranjang')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-5xl">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('home') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-navy-900">Keranjang Belanja</h1>
    </div>

    @if(count($cartItems) > 0)
    <div class="lg:grid lg:grid-cols-3 lg:gap-8">
        <!-- Cart Items List -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                @foreach($cartItems as $item)
                <div class="p-4 border-b border-gray-50 last:border-0 flex gap-4 items-center">
                    <!-- Image -->
                    <div class="w-20 h-20 bg-gray-100 rounded-xl overflow-hidden flex-shrink-0">
                        @if($item['image'])
                            <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-300">
                                <i class="fas fa-utensils"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-navy-900 truncate">{{ $item['name'] }}</h3>

                        <p class="font-bold text-orange-600">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center gap-3">
                        <form action="{{ route('cart.update', $item['id']) }}" method="POST" class="flex items-center bg-gray-50 rounded-lg p-1 border border-gray-200">
                            @csrf
                            @method('PATCH')
                            <button type="submit" name="quantity" value="{{ $item['quantity'] - 1 }}" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-orange-600 transition rounded-md hover:bg-white" {{ $item['quantity'] <= 1 ? 'disabled' : '' }}>
                                <i class="fas fa-minus text-xs"></i>
                            </button>
                            <span class="w-8 text-center text-sm font-bold text-navy-900 font-mono">{{ $item['quantity'] }}</span>
                            <button type="submit" name="quantity" value="{{ $item['quantity'] + 1 }}" class="w-7 h-7 flex items-center justify-center text-gray-500 hover:text-orange-600 transition rounded-md hover:bg-white">
                                <i class="fas fa-plus text-xs"></i>
                            </button>
                        </form>

                        <form action="{{ route('cart.remove', $item['id']) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 flex items-center justify-center hover:bg-red-100 transition">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Summary -->
        <div class="lg:col-span-1 mt-6 lg:mt-0">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sticky top-24">
                <h3 class="font-bold text-navy-900 mb-4 text-lg">Ringkasan Pesanan</h3>

                <div class="mb-6 p-4 bg-orange-50 rounded-xl border border-orange-100">
                    <p class="text-sm text-orange-800 font-medium">
                        <i class="fas fa-chair mr-1"></i>
                        Lokasi: {{ $tableNumber ? 'Meja ' . $tableNumber : 'Belum terdeteksi (scan QR meja)' }}
                    </p>
                </div>

                <div class="space-y-3 text-sm text-gray-600 mb-6">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span class="font-medium">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Layanan</span>
                        <span class="font-medium">Rp {{ number_format($serviceFee, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Biaya Pengiriman</span>
                        <span class="font-medium">
                            Rp {{ number_format($deliveryFee, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <div class="border-t border-dashed border-gray-200 pt-4 flex justify-between items-center mb-6">
                    <span class="font-bold text-lg text-navy-900">Total Pembayaran</span>
                    <span class="font-bold text-xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <div class="flex flex-col gap-3">
                    <a href="{{ route('orders.checkout') }}" class="w-full py-3.5 px-6 rounded-xl bg-navy-900 text-white font-bold text-center hover:bg-navy-800 shadow-lg shadow-navy-900/20 transition flex items-center justify-center gap-2">
                        Lanjut Bayar <i class="fas fa-arrow-right"></i>
                    </a>
                    <a href="{{ route('home') }}" class="w-full py-3.5 px-6 rounded-xl border border-gray-200 font-bold text-gray-600 hover:bg-gray-50 transition text-center">
                        Tambah Menu Lain
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="text-center py-16 bg-white rounded-3xl border border-dashed border-gray-200">
        <div class="w-24 h-24 bg-orange-50 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fas fa-shopping-basket text-4xl text-orange-300"></i>
        </div>
        <h2 class="text-xl font-bold text-navy-900 mb-2">Keranjang Kosong</h2>
        <p class="text-gray-500 mb-8 max-w-xs mx-auto">Sepertinya Anda belum memesan apapun. Yuk cari menu favorit Anda!</p>
        <a href="{{ route('home') }}" class="inline-flex items-center gap-2 bg-orange-600 text-white px-8 py-3 rounded-full font-bold hover:bg-orange-700 transition shadow-lg shadow-orange-500/30">
            Mulai Pesan <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    @endif
</div>
@endsection
