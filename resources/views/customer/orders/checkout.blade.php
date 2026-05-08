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

    {{-- Error Alert --}}
    @if(session('error'))
    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3">
        <i class="fas fa-exclamation-circle text-xl flex-shrink-0"></i>
        <span>{{ session('error') }}</span>
    </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
    <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-xl mb-6">
        <div class="flex items-center gap-2 font-bold mb-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span>Mohon perbaiki kesalahan berikut:</span>
        </div>
        <ul class="list-disc list-inside text-sm space-y-1">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif

    <form id="checkoutForm" action="{{ route('orders.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-8" novalidate>
        @csrf

        {{-- ===================== LEFT COLUMN ===================== --}}
        <div class="md:col-span-2 space-y-6">

            {{-- Step 1: Customer Info --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-orange-100 text-orange-600 flex items-center justify-center text-sm font-bold">1</span>
                    Informasi Pemesan
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    {{-- Nama Lengkap --}}
                    <div class="md:col-span-2">
                        <label for="customer_name" class="block text-sm font-bold text-navy-900 mb-2">
                            Nama Lengkap <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="customer_name"
                            type="text"
                            name="customer_name"
                            value="{{ old('customer_name') }}"
                            required
                            autocomplete="name"
                            class="w-full px-5 py-3 rounded-xl border @error('customer_name') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition"
                            placeholder="Masukkan nama lengkap Anda"
                        >
                        @error('customer_name')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Nomor HP --}}
                    <div>
                        <label for="customer_phone" class="block text-sm font-bold text-navy-900 mb-2">
                            Nomor HP / WhatsApp <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-sm font-medium">+62</span>
                            <input
                                id="customer_phone"
                                type="tel"
                                name="customer_phone"
                                value="{{ old('customer_phone') }}"
                                required
                                autocomplete="tel"
                                class="w-full pl-12 pr-5 py-3 rounded-xl border @error('customer_phone') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition"
                                placeholder="812xxxxxxxx"
                                inputmode="tel"
                            >
                        </div>
                        @error('customer_phone')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @else
                            <p class="text-gray-400 text-xs mt-1.5">Contoh: 81234567890 (tanpa awalan 0)</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label for="customer_email" class="block text-sm font-bold text-navy-900 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input
                            id="customer_email"
                            type="email"
                            name="customer_email"
                            value="{{ old('customer_email') }}"
                            required
                            autocomplete="email"
                            class="w-full px-5 py-3 rounded-xl border @error('customer_email') border-red-400 bg-red-50 @else border-gray-200 bg-gray-50 @enderror focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 focus:bg-white transition"
                            placeholder="email@contoh.com"
                            inputmode="email"
                        >
                        @error('customer_email')
                            <p class="text-red-500 text-xs mt-1.5 flex items-center gap-1">
                                <i class="fas fa-exclamation-circle"></i> {{ $message }}
                            </p>
                        @else
                            <p class="text-gray-400 text-xs mt-1.5">Untuk konfirmasi pembayaran iPaymu</p>
                        @enderror
                    </div>
                </div>

                {{-- iPaymu prefill notice --}}
                <div class="mt-5 p-3 bg-green-50 border border-green-100 rounded-lg flex items-start gap-2.5">
                    <i class="fas fa-magic text-green-500 text-sm mt-0.5 flex-shrink-0"></i>
                    <p class="text-xs text-green-800">
                        Data ini akan <strong>otomatis terisi</strong> di halaman pembayaran iPaymu — Anda tidak perlu mengisi ulang.
                    </p>
                </div>
            </div>

            {{-- Step 2: Lokasi --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-sm font-bold">2</span>
                    Lokasi Meja
                </h3>

                <div class="grid grid-cols-1 gap-5">
                    <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Nomor Meja</label>
                        <div class="flex items-center gap-2 text-navy-900 font-bold text-lg">
                            <i class="fas fa-chair text-orange-500"></i>
                            {{ $tableNumber ?? 'Lokasi Tidak Terdeteksi' }}
                        </div>
                        <input type="hidden" name="table_number" value="{{ $tableNumber }}">
                    </div>

                    <div>
                        <label for="notes" class="block text-sm font-bold text-navy-900 mb-2">Catatan Pesanan <span class="text-gray-400 font-normal text-xs">(Opsional)</span></label>
                        <textarea
                            id="notes"
                            name="notes"
                            rows="2"
                            class="w-full px-5 py-3 rounded-xl border border-gray-200 focus:outline-none focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500 transition bg-gray-50 focus:bg-white resize-none"
                            placeholder="Contoh: Jangan terlalu pedas, tambah saus..."
                        >{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Step 3: Metode Pembayaran --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8">
                <h3 class="font-bold text-navy-900 mb-6 flex items-center gap-3 text-lg">
                    <span class="w-8 h-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-sm font-bold">3</span>
                    Metode Pembayaran
                </h3>

                {{-- Payment Option: QRIS via iPaymu --}}
                <label class="relative cursor-pointer block">
                    <input type="radio" name="payment_method" value="qris" class="peer sr-only" checked>
                    <div class="p-5 rounded-xl border-2 border-orange-500 bg-orange-50/40 peer-checked:border-orange-500 peer-checked:bg-orange-50/60 transition-all">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-xl bg-white border border-orange-100 flex items-center justify-center shadow-sm">
                                    <i class="fas fa-qrcode text-2xl text-orange-600"></i>
                                </div>
                                <div>
                                    <span class="font-bold text-navy-900 block">QRIS</span>
                                    <p class="text-xs text-gray-500">Bayar dengan semua dompet digital & m-Banking</p>
                                </div>
                            </div>
                            <i class="fas fa-check-circle text-orange-500 text-xl"></i>
                        </div>
                    </div>
                </label>

                {{-- iPaymu Redirect Info --}}
                <div class="mt-5 rounded-xl border border-blue-100 bg-blue-50 overflow-hidden">
                    <div class="flex items-center gap-3 bg-blue-100/60 px-5 py-3 border-b border-blue-100">
                        <i class="fas fa-info-circle text-blue-600"></i>
                        <span class="font-bold text-blue-900 text-sm">Alur Pembayaran</span>
                        <span class="ml-auto text-xs bg-yellow-200 text-yellow-800 font-bold px-2 py-0.5 rounded-full">SANDBOX</span>
                    </div>
                    <div class="px-5 py-4 space-y-3">
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">1</div>
                            <p class="text-sm text-blue-800">Klik <strong>"Buat &amp; Bayar Pesanan"</strong> — data Anda terkirim ke iPaymu otomatis</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">2</div>
                            <p class="text-sm text-blue-800">Anda diarahkan ke <strong>halaman iPaymu Sandbox</strong> untuk memilih bank / dompet digital</p>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-blue-200 text-blue-800 flex items-center justify-center text-xs font-bold flex-shrink-0 mt-0.5">3</div>
                            <p class="text-sm text-blue-800">Setelah bayar, Anda kembali otomatis ke halaman konfirmasi pesanan</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>{{-- end left column --}}

        {{-- ===================== RIGHT COLUMN: Order Summary ===================== --}}
        <div class="md:col-span-1">
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 sticky top-24">
                <h3 class="font-bold text-navy-900 mb-6 text-lg">Ringkasan Pesanan</h3>

                {{-- Items --}}
                <div class="space-y-4 mb-6 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                    @foreach($cartItems as $item)
                    <div class="flex gap-3">
                        <div class="w-12 h-12 rounded-lg bg-gray-100 overflow-hidden flex-shrink-0">
                            @if($item['image'])
                                <img src="{{ asset('storage/' . $item['image']) }}" class="w-full h-full object-cover" alt="{{ $item['name'] }}">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-300">
                                    <i class="fas fa-utensils"></i>
                                </div>
                            @endif
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-bold text-navy-900 text-sm truncate">{{ $item['name'] }}</p>
                            <p class="text-xs text-gray-500">{{ $item['quantity'] }} × Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                        </div>
                        <p class="text-sm font-bold text-orange-600 whitespace-nowrap">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                    </div>
                    @endforeach
                </div>

                {{-- Totals --}}
                <div class="space-y-3 text-sm text-gray-600 mb-6 pt-5 border-t border-dashed border-gray-200">
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

                <div class="flex justify-between items-center mb-8 bg-orange-50 rounded-xl px-4 py-3 border border-orange-100">
                    <span class="font-bold text-lg text-navy-900">Total</span>
                    <span class="font-bold text-2xl text-orange-600">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                {{-- Submit Button --}}
                <button
                    id="submitBtn"
                    type="submit"
                    class="w-full py-4 bg-navy-900 text-white rounded-xl font-bold text-base hover:bg-navy-800 shadow-lg shadow-navy-900/20 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2 disabled:opacity-60 disabled:cursor-not-allowed disabled:translate-y-0"
                >
                    <span id="submitBtnContent" class="flex items-center gap-2">
                        <i class="fas fa-lock text-sm"></i>
                        Buat &amp; Bayar Pesanan
                    </span>
                    <span id="submitBtnLoading" class="hidden items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        Memproses, mohon tunggu...
                    </span>
                </button>

                <p class="text-center text-xs text-gray-400 mt-4">
                    <i class="fas fa-shield-alt mr-1"></i>
                    Pembayaran aman melalui iPaymu Sandbox
                </p>
            </div>
        </div>

    </form>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkoutForm');
    const btn = document.getElementById('submitBtn');
    const btnContent = document.getElementById('submitBtnContent');
    const btnLoading = document.getElementById('submitBtnLoading');

    // Format phone: strip leading 0 or +62, keep digits only
    const phoneInput = document.getElementById('customer_phone');
    phoneInput.addEventListener('input', function () {
        let val = this.value.replace(/\D/g, '');
        // Strip leading 62 or 0 for display consistency
        if (val.startsWith('62')) val = val.slice(2);
        if (val.startsWith('0')) val = val.slice(1);
        this.value = val;
    });

    // Loading state on submit
    form.addEventListener('submit', function (e) {
        // Basic HTML5 validation
        if (!form.checkValidity()) {
            form.reportValidity();
            e.preventDefault();
            return;
        }
        btn.disabled = true;
        btnContent.classList.add('hidden');
        btnContent.classList.remove('flex');
        btnLoading.classList.remove('hidden');
        btnLoading.classList.add('flex');
    });
});
</script>
@endpush
@endsection
