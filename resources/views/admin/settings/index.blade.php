@extends('layouts.admin')

@section('title', 'Pengaturan')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            
            <h3 class="font-semibold text-navy border-b pb-2">Informasi Toko</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Toko</label>
                <input type="text" name="store_name" value="{{ $settings['store_name'] }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat</label>
                <textarea name="store_address" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange">{{ $settings['store_address'] }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Telepon</label>
                <input type="text" name="store_phone" value="{{ $settings['store_phone'] }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange">
            </div>

            <h3 class="font-semibold text-navy border-b pb-2 pt-4">Biaya</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Layanan (Rp)</label>
                <input type="number" name="service_fee" value="{{ $settings['service_fee'] }}" required min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange">
            </div>

            <h3 class="font-semibold text-navy border-b pb-2 pt-4">Pembayaran QRIS</h3>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Gambar QRIS</label>
                @if($settings['qris_image'])
                <div class="mb-2">
                    <img src="{{ asset('storage/' . $settings['qris_image']) }}" class="w-48 h-48 object-contain border rounded-lg">
                </div>
                @endif
                <input type="file" name="qris_image" accept="image/*" class="w-full px-4 py-2 border rounded-lg bg-white">
                <p class="text-xs text-gray-500 mt-1">Upload gambar QRIS statis Anda. Ini akan ditampilkan ke pelanggan saat checkout.</p>
            </div>

            <h3 class="font-semibold text-navy border-b pb-2 pt-4">
                <i class="fas fa-credit-card mr-2 text-blue-500"></i>
                Payment Gateway
            </h3>
            
            <div class="bg-blue-50 border border-blue-100 rounded-lg p-4">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-blue-500 mt-0.5"></i>
                    <div>
                        <p class="text-sm font-semibold text-blue-900">Segera Hadir</p>
                        <p class="text-xs text-blue-700 mt-1">
                            Integrasi payment gateway (Midtrans, Xendit, dll) akan segera tersedia. 
                            Saat ini pembayaran dilakukan secara manual via QRIS statis dan diverifikasi oleh admin.
                        </p>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Payment Gateway Provider</label>
                <select name="payment_gateway" disabled class="w-full px-4 py-2 border rounded-lg bg-gray-100 text-gray-500">
                    <option value="manual" selected>Manual (Verifikasi Admin)</option>
                    <option value="midtrans">Midtrans</option>
                    <option value="xendit">Xendit</option>
                    <option value="doku">Doku</option>
                    <option value="tripay">Tripay</option>
                </select>
                <p class="text-xs text-gray-400 mt-1">Akan diaktifkan setelah integrasi payment gateway selesai.</p>
            </div>

            <button type="submit" class="w-full py-3 bg-orange text-white rounded-lg hover:bg-orange-hover transition font-semibold">
                <i class="fas fa-save mr-2"></i> Simpan Pengaturan
            </button>
        </form>
    </div>
</div>
@endsection
