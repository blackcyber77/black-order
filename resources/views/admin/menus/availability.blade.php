@extends('layouts.admin')

@section('title', 'Ketersediaan Menu QR')

@section('content')
<div class="flex justify-between items-center mb-6">
    <div>
        <h2 class="text-lg font-semibold text-navy">Atur Ketersediaan Menu</h2>
        <p class="text-sm text-gray-500 mt-1">Status di halaman ini menentukan menu yang tampil di POS dan pemesanan QR Code.</p>
    </div>
    <a href="{{ route('admin.menus.index') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-sm hover:bg-gray-50 transition">
        <i class="fas fa-list mr-2"></i> Manajemen Menu
    </a>
</div>

<form method="GET" action="{{ route('admin.menus.availability') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-center">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu..." class="px-4 py-2 border rounded-lg text-sm flex-1 min-w-[220px]">
    <select name="category_id" class="px-4 py-2 border rounded-lg text-sm" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
            {{ $category->name }}
        </option>
        @endforeach
    </select>
    <button type="submit" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm hover:bg-navy-800 transition">Filter</button>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status Saat Ini</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($menus as $menu)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center text-gray-400">
                            @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                            @else
                            <i class="fas fa-utensils"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-navy-900">{{ $menu->name }}</p>
                            <p class="text-xs text-gray-500">{{ $menu->formatted_price }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm">{{ $menu->category?->name ?? '-' }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $menu->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $menu->is_available ? 'Tersedia (Tampil di QR)' : 'Tidak Tersedia (Disembunyikan)' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <form action="{{ route('admin.menus.toggle-availability', $menu) }}" method="POST" class="inline">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="px-3 py-1.5 rounded-lg text-xs font-semibold {{ $menu->is_available ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }} transition">
                            {{ $menu->is_available ? 'Sembunyikan dari QR' : 'Tampilkan di QR' }}
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Belum ada menu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $menus->appends(request()->query())->links() }}
</div>
@endsection

