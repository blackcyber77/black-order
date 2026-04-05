@extends('layouts.customer')

@section('title', 'Checkout')

@section('content')
<div class="container mx-auto px-4 py-8 max-w-6xl">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('cart.index') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
            <i class="fas fa-arrow-left"></i>
        </a>
        <h1 class="text-2xl font-bold text-navy-900">Checkout Pesanan</h1>
    </div>

    @if(session('error'))
    <div class="bg-red-50 border border-red-100 text-red-600 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    <form action="{{ route('orders.store') }}" method="POST" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @csrf
        
        <!-- Left Column: Customer Data & Payment -->
        <div class="md:col-span-2 space-y-6">
            
            <!-- Customer Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-sm">1</span>
                    Informasi Pemesan
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-navy-900 mb-2">Nama Lengkap</label>
                        <input type="text" name="customer_name" value="{{ old('customer_name') }}" required
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition bg-gray-50 focus:bg-white"
                            placeholder="Lendra">
                        @error('customer_name')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-navy-900 mb-2">Nomor HP / WhatsApp</label>
                        <input type="text" name="customer_phone" value="{{ old('customer_phone') }}" required
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition bg-gray-50 focus:bg-white"
                            placeholder="0812...">
                        @error('customer_phone')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-navy-900 mb-2">Email (Opsional)</label>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition bg-gray-50 focus:bg-white"
                            placeholder="email@contoh.com">
                    </div>
                </div>
            </div>

            <!-- Location (Read-only from QR scan) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm">2</span>
                    Lokasi Pengiriman
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200 md:col-span-2">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor Meja</label>
                        <div class="flex items-center gap-2 text-navy-900 font-bold text-lg">
                            <i class="fas fa-chair text-orange-500"></i>
                            {{ $tableNumber ?? 'Lokasi Tidak Terdeteksi' }}
                        </div>
                        <input type="hidden" name="table_number" value="{{ $tableNumber }}">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-bold text-navy-900 mb-2">Catatan Pesanan (Opsional)</label>
                        <textarea name="notes" rows="2"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition bg-gray-50 focus:bg-white"
                            placeholder="Contoh: Jangan terlalu pedas, tambah saus..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Payment Method (Cashless Only for QR ordering) -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm">3</span>
                    Metode Pembayaran
                </h3>

                <!-- Cashless Notice -->
                <div class="bg-blue-50 border border-blue-100 rounded-xl p-4 mb-6 flex items-start gap-3">
                    <i class="fas fa-shield-alt text-blue-500 text-lg mt-0.5"></i>
                    <div>
                        <p class="text-sm font-bold text-blue-900">Pembayaran Cashless</p>
                        <p class="text-xs text-blue-700 mt-1">Pemesanan via QR Code hanya menerima pembayaran digital (QRIS).</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <!-- QRIS Option -->
                    <label class="relative cursor-pointer group">
                        <input type="radio" name="payment_method" value="qris" class="peer sr-only" checked>
                        <div class="p-5 rounded-xl border-2 border-orange-500 bg-orange-50/50 transition-all peer-checked:border-orange-500 peer-checked:bg-orange-50/50">
                            <div class="flex items-center gap-3 pb-2">
                                <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center">
                                    <i class="fas fa-qrcode text-xl text-orange-600"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-navy-900">QRIS</span>
                                    <p class="text-xs text-gray-500">Scan QRIS dan upload bukti pembayaran</p>
                                </div>
                            </div>
                        </div>
                        <div class="absolute top-4 right-4 text-orange-500 opacity-100 transition-opacity peer-checked:opacity-100">
                            <i class="fas fa-check-circle text-xl"></i>
                        </div>
                    </label>
                </div>

                <!-- QRIS Payment Section -->
                <div id="qris_section" class="mt-6 animate-fade-in-down">
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-200 flex flex-col items-center text-center">
                        <p class="font-bold text-navy-900 mb-2">Scan QRIS di bawah ini</p>
                        <p class="text-xs text-gray-500 mb-4">Gunakan aplikasi e-wallet atau mobile banking Anda</p>
                        
                        @if($qrisImage)
                            <div class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 mb-4 inline-block">
                                <img src="{{ asset('storage/' . $qrisImage) }}" alt="QRIS Code" class="h-48 object-contain">
                            </div>
                        @else
                            <div class="w-48 h-48 bg-gray-200 rounded-xl flex items-center justify-center mb-4 text-gray-400">
                                <div class="text-center">
                                    <i class="fas fa-qrcode text-4xl mb-2"></i>
                                    <p class="text-xs">QRIS belum diatur</p>
                                </div>
                            </div>
                        @endif

                        <div class="w-full max-w-sm">
                            <label class="block text-sm font-bold text-navy-900 mb-2 text-left">Upload Bukti Pembayaran</label>
                            <input type="file" name="payment_proof" accept="image/*" class="w-full text-sm text-gray-500
                                file:mr-4 file:py-2.5 file:px-4
                                file:rounded-full file:border-0
                                file:text-sm file:font-bold
                                file:bg-orange-50 file:text-orange-700
                                hover:file:bg-orange-100 transition
                            ">
                            <p class="text-xs text-gray-500 mt-2 text-left">Format: JPG, PNG. Maks: 2MB</p>
                        </div>

                        <!-- Payment status info -->
                        <div class="mt-4 p-3 bg-yellow-50 border border-yellow-100 rounded-lg w-full max-w-sm">
                            <p class="text-xs text-yellow-800">
                                <i class="fas fa-info-circle mr-1"></i>
                                Pembayaran akan diverifikasi oleh admin. Anda akan mendapat notifikasi setelah pembayaran dikonfirmasi.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Order Summary -->
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 sticky top-24">
                <h3 class="font-bold text-navy-900 mb-6 text-lg">Ringkasan</h3>
                
                <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-xs text-gray-400">IMG</div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-navy-900 text-sm truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-bold text-orange-600">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                <div class="space-y-3 text-sm text-gray-600 mb-6 pt-6 border-t border-dashed border-gray-200">
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
                        <span class="font-medium">Rp {{ number_format($deliveryFee, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex justify-between items-center mb-8">
                    <span class="font-bold text-lg text-navy-900">Total</span>
                    <span class="font-bold text-2xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <button type="submit" class="w-full py-4 bg-navy-900 text-white rounded-xl font-bold text-lg hover:bg-navy-800 shadow-lg shadow-navy-900/20 hover:-translate-y-1 transition-all duration-300 flex items-center justify-center gap-2">
                    <i class="fas fa-lock text-sm"></i> Buat Pesanan
                </button>
                <p class="text-center text-xs text-gray-400 mt-4">
                    Dengan memesan, Anda setuju dengan S&K kami.
                </p>
            </div>
        </div>
    </form>
</div>
@endsection
