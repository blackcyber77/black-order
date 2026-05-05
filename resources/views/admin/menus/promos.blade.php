@extends('layouts.admin')

@section('title', 'Promo & Bundling')

@section('content')
@if(isset($errors) && $errors->any())
<div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-5 py-4 rounded-xl">
    <p class="font-semibold mb-2">Data promo belum tersimpan:</p>
    <ul class="list-disc pl-5 space-y-1 text-sm">
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

@if(empty($promoFeatureReady) || !$promoFeatureReady)
<div class="mb-6 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-xl">
    Fitur promo/bundling belum aktif di database. Jalankan migrasi terbaru lalu refresh halaman ini.
</div>
@endif

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-6">
            <h3 class="font-semibold text-navy-900 mb-4">Buat / Update Promo</h3>
            <form action="{{ route('admin.menus.promos.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium mb-1">Menu</label>
                    <select name="menu_item_id" required class="w-full px-3 py-2 border rounded-lg" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                        <option value="">Pilih menu</option>
                        @foreach($menuItems as $item)
                        <option value="{{ $item->id }}" {{ (string) old('menu_item_id') === (string) $item->id ? 'selected' : '' }}>
                            {{ $item->name }} ({{ $item->category?->name ?? '-' }})
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Jenis</label>
                    <select name="promo_type" required class="w-full px-3 py-2 border rounded-lg" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                        <option value="promo" {{ old('promo_type') === 'promo' ? 'selected' : '' }}>Promo</option>
                        <option value="bundling" {{ old('promo_type') === 'bundling' ? 'selected' : '' }}>Bundling</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Judul Promo</label>
                    <input type="text" name="promo_title" value="{{ old('promo_title') }}" required maxlength="120" class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: Hemat Pagi 20%" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-medium mb-1">Harga Coret (opsional)</label>
                        <input type="text" name="promo_original_price" value="{{ old('promo_original_price') }}" class="w-full px-3 py-2 border rounded-lg" placeholder="Contoh: 25.000" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Urutan Atas</label>
                        <input type="number" name="promo_sort_order" min="0" value="{{ old('promo_sort_order', 0) }}" class="w-full px-3 py-2 border rounded-lg" placeholder="0" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                    </div>
                </div>

                <button type="submit" class="w-full py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition font-semibold disabled:opacity-50 disabled:cursor-not-allowed" {{ empty($promoFeatureReady) || !$promoFeatureReady ? 'disabled' : '' }}>
                    Simpan Promo/Bundling
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <div class="px-6 py-4 border-b">
                <h3 class="font-semibold text-navy-900">Promo Aktif (Akan Muncul Paling Atas di Menu)</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Menu</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Jenis</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Urutan</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($promoMenus as $menu)
                        <tr>
                            <td class="px-6 py-4 text-sm font-medium">{{ $menu->name }}</td>
                            <td class="px-6 py-4 text-sm">
                                <span class="px-2 py-1 rounded-full text-xs {{ $menu->promo_type === 'bundling' ? 'bg-blue-100 text-blue-700' : 'bg-orange-100 text-orange-700' }}">
                                    {{ strtoupper($menu->promo_type) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm">{{ $menu->promo_title }}</td>
                            <td class="px-6 py-4 text-sm">{{ $menu->promo_sort_order }}</td>
                            <td class="px-6 py-4 text-sm">
                                <form action="{{ route('admin.menus.promos.destroy', $menu) }}" method="POST" onsubmit="return confirm('Hapus status promo menu ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:underline">Hapus Promo</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada promo/bundling aktif.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
