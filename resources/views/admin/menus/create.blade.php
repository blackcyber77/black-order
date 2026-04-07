@extends('layouts.admin')

@section('title', 'Tambah Menu POS')

@section('content')
<div class="max-w-2xl">
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.menus.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Menu *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                    @error('name')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kategori *</label>
                    <select name="category_id" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
                        <option value="">Pilih kategori</option>
                        @foreach($categories as $category)
                        <option value="{{ $category->id }}" {{ (string) old('category_id') === (string) $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('category_id')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Harga *</label>
                    <input type="number" name="price" min="0" step="500" value="{{ old('price') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="15000">
                    @error('price')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Menu</label>
                    <input type="file" name="image" accept="image/*" class="w-full px-4 py-2 border rounded-lg bg-white">
                    @error('image')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_available" value="1" id="is_available" {{ old('is_available', '1') ? 'checked' : '' }} class="rounded text-orange-600 focus:ring-orange-500">
                <label for="is_available" class="ml-2 text-sm text-gray-700">Tersedia untuk POS & QR Order</label>
            </div>

            <div class="flex flex-col gap-3 pt-6 border-t border-gray-100">
                <button type="submit" class="w-full py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-bold shadow-lg shadow-orange-500/20">
                    Simpan Menu
                </button>
                <button type="submit" name="save_and_create_another" value="1" class="w-full py-3 bg-white text-orange-600 border border-orange-200 rounded-xl hover:bg-orange-50 transition font-bold shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Simpan & Tambah Lagi
                </button>
                <a href="{{ route('admin.menus.index') }}" class="w-full py-3 border border-gray-200 text-gray-600 text-center rounded-xl hover:bg-gray-50 transition font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

