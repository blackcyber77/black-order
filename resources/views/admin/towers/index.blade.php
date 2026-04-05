@extends('layouts.admin')

@section('title', 'Tower & Meja')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold text-navy">Daftar Tower</h2>
    <a href="{{ route('admin.towers.create') }}" class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-500/20">
        <i class="fas fa-plus mr-2"></i> Tambah Tower
    </a>
</div>

<div class="bg-white rounded-xl shadow overflow-hidden">
    <table class="w-full">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nama Tower</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ongkir</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Jumlah Meja</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y">
            @forelse($towers as $tower)
            <tr>
                <td class="px-6 py-4 font-medium text-navy">{{ $tower->name }}</td>
                <td class="px-6 py-4 text-sm text-orange-600">Rp {{ number_format($tower->delivery_fee, 0, ',', '.') }}</td>
                <td class="px-6 py-4 text-sm">{{ $tower->dining_tables_count }} meja</td>
                <td class="px-6 py-4">
                    <span class="px-2 py-1 text-xs rounded-full {{ $tower->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $tower->is_active ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </td>
                <td class="px-6 py-4 text-sm space-x-2">
                    <a href="{{ route('admin.towers.tables', $tower) }}" class="text-purple-600 hover:underline">Meja</a>
                    <a href="{{ route('admin.towers.edit', $tower) }}" class="text-blue-600 hover:underline">Edit</a>
                    <form action="{{ route('admin.towers.destroy', $tower) }}" method="POST" class="inline" onsubmit="return confirm('Hapus tower ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-8 text-center text-gray-500">Belum ada tower</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
