@extends('layouts.admin')

@section('title', 'Tambah Kategori')

@section('content')
<div class="max-w-md">
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                <input type="text" name="icon" value="{{ old('icon') }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500" placeholder="🍔 🍕 🍜">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description') }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex flex-col gap-3 pt-6 border-t border-gray-100">
                <button type="submit" class="w-full py-3 bg-orange-500 text-white rounded-xl hover:bg-orange-600 transition font-bold shadow-lg shadow-orange-500/20">
                    Simpan
                </button>
                <button type="submit" name="save_and_create_another" value="1" class="w-full py-3 bg-white text-orange-600 border border-orange-200 rounded-xl hover:bg-orange-50 transition font-bold shadow-sm">
                    <i class="fas fa-plus mr-2"></i> Simpan & Tambah Lagi
                </button>
                <a href="{{ route('admin.categories.index') }}" class="w-full py-3 border border-gray-200 text-gray-600 text-center rounded-xl hover:bg-gray-50 transition font-medium">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
