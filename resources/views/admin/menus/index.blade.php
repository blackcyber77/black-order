@extends('layouts.admin')

@section('title', 'Manajemen Menu POS')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold text-navy">Daftar Menu</h2>
    <a href="{{ route('admin.menus.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-500/20">
        <i class="fas fa-plus mr-2"></i> Tambah Menu
    </a>
</div>

<form method="GET" action="{{ route('admin.menus.index') }}" class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 mb-4 flex flex-wrap gap-3 items-center">
    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari menu..." class="px-4 py-2 border rounded-lg text-sm flex-1 min-w-[220px]">

    <select name="category_id" class="px-4 py-2 border rounded-lg text-sm" onchange="this.form.submit()">
        <option value="">Semua Kategori</option>
        @foreach($categories as $category)
        <option value="{{ $category->id }}" {{ (string) request('category_id') === (string) $category->id ? 'selected' : '' }}>
            {{ $category->name }}
        </option>
        @endforeach
    </select>

    <select name="availability" class="px-4 py-2 border rounded-lg text-sm" onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="available" {{ request('availability') === 'available' ? 'selected' : '' }}>Tersedia</option>
        <option value="unavailable" {{ request('availability') === 'unavailable' ? 'selected' : '' }}>Tidak Tersedia</option>
    </select>

    <button type="submit" class="px-4 py-2 bg-navy-900 text-white rounded-lg text-sm hover:bg-navy-800 transition">Filter</button>
</form>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Menu</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Kategori</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Harga</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ketersediaan</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($menus as $menu)
            <tr>
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-lg overflow-hidden bg-gray-100 flex items-center justify-center text-gray-400">
                            @if($menu->image)
                            <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-full h-full object-cover">
                            @else
                            <i class="fas fa-utensils"></i>
                            @endif
                        </div>
                        <div>
                            <p class="font-semibold text-navy-900">{{ $menu->name }}</p>
                            <p class="text-xs text-gray-500 line-clamp-1">{{ $menu->description }}</p>
                        </div>
                    </div>
                </td>
                <td class="px-6 py-4 text-sm">{{ $menu->category?->name ?? '-' }}</td>
                <td class="px-6 py-4 text-sm font-semibold text-orange-600">{{ $menu->formatted_price }}</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $menu->is_available ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $menu->is_available ? 'Tersedia' : 'Tidak Tersedia' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm">
                    <form action="{{ route('admin.menus.toggle-availability', $menu) }}" method="POST" class="inline mr-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="text-indigo-600 hover:underline">{{ $menu->is_available ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                    </form>
                    <a href="{{ route('admin.menus.edit', $menu) }}" class="text-blue-600 hover:underline mr-2">Edit</a>
                    <form action="{{ route('admin.menus.destroy', $menu) }}" method="POST" class="inline" onsubmit="return confirm('Hapus menu ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada menu</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-4">
    {{ $menus->appends(request()->query())->links() }}
</div>
@endsection
