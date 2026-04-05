@extends('layouts.admin')

@section('title', 'Edit Kategori')

@section('content')
<div class="max-w-md">
    <div class="bg-white rounded-xl shadow p-6">
        <form action="{{ route('admin.categories.update', $category) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori *</label>
                <input type="text" name="name" value="{{ old('name', $category->name) }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Icon (Emoji)</label>
                <input type="text" name="icon" value="{{ old('icon', $category->icon) }}" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
                <textarea name="description" rows="2" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">{{ old('description', $category->description) }}</textarea>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="sort_order" value="{{ old('sort_order', $category->sort_order) }}" min="0" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex items-center">
                <input type="checkbox" name="is_active" value="1" id="is_active" {{ $category->is_active ? 'checked' : '' }} class="rounded text-orange-600 focus:ring-orange-500">
                <label for="is_active" class="ml-2 text-sm text-gray-700">Kategori Aktif</label>
            </div>

            <div class="flex gap-4 pt-4">
                <a href="{{ route('admin.categories.index') }}" class="flex-1 py-2 border border-gray-300 text-center rounded-lg hover:bg-gray-50 transition">Batal</a>
                <button type="submit" class="flex-1 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection
