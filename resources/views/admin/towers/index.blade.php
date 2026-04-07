@extends('layouts.admin')

@section('title', 'Manajemen Meja')

@section('content')
<div class="flex items-center gap-3 mb-2">
    <h2 class="text-lg font-semibold text-navy">Daftar Meja</h2>
</div>
<p class="text-sm text-gray-500 mb-6">Fitur tambah/edit/hapus tower dinonaktifkan. Saat ini hanya bisa menambahkan meja.</p>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
            <h3 class="font-semibold text-navy mb-4">Tambah Meja</h3>
            <form action="{{ route('admin.tables.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm text-slate-700 mb-1">Nomor Meja</label>
                    <input type="text" name="table_number" value="{{ old('table_number') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" placeholder="Contoh: A-01">
                    @error('table_number')
                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition shadow-lg shadow-orange-500/20">
                    <i class="fas fa-plus mr-2"></i> Tambah Meja
                </button>
            </form>
        </div>
    </div>

    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nomor Meja</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($tables as $table)
                    <tr>
                        <td class="px-6 py-4 font-medium text-navy">{{ $table->table_number }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs rounded-full {{ $table->status === 'terisi' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800' }}">
                                {{ ucfirst($table->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm space-x-2">
                            <a href="{{ route('admin.tables.qr', $table) }}" class="text-blue-600 hover:underline" target="_blank">QR</a>
                            <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="inline" onsubmit="return confirm('Hapus meja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500">Belum ada meja</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-gray-50">
                {{ $tables->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
