@extends('layouts.admin')

@section('title', 'Edit Menu POS')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.menus.update', $menu) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name', $menu->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id', $menu->category_id) === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description', $menu->description) }}</textarea>
                @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga *</label>
                    <input type="number" name="price" min="0" step="500" value="{{ old('price', (int) $menu->price) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Menu</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg bg-white">
                    @error('image')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            @if($menu->image)
            <div>
                <p class="text-sm text-gray-600 mb-2">Gambar Saat Ini</p>
                <img src="{{ asset('storage/' . $menu->image) }}" alt="{{ $menu->name }}" class="w-32 h-32 object-cover rounded-lg border">
            </div>
            @endif

            <div class="flex items-center">
                <input type="checkbox" name="is_available" value="1" id="is_available" {{ old('is_available', $menu->is_available) ? 'checked' : '' }} class="rounded text-orange-600 focus:ring-orange-500">
                <label for="is_available" class="ml-2 text-sm text-gray-700">Tersedia untuk POS & QR Order</label>
            </div>

            <div class="flex gap-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.menus.index') }}" class="flex-1 py-3 border border-gray-300 text-center rounded-xl hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="flex-1 py-3 bg-orange-600 text-white rounded-xl hover:bg-orange-700 transition">Update Menu</button>
            </div>
        </form>
    </div>
</div>
@endsection

