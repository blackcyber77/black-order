@extends('layouts.admin')

@section('title', 'Meja - ' . $tower->name)

@section('content')
<div class="flex items-center gap-4 mb-6">
    <a href="{{ route('admin.towers.index') }}" class="w-10 h-10 rounded-full bg-white border border-gray-200 flex items-center justify-center text-gray-600 hover:bg-orange-50 hover:text-orange-600 hover:border-orange-200 transition">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h2 class="text-lg font-semibold text-navy">Daftar Meja di {{ $tower->name }}</h2>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Add Room Form -->
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100 sticky top-24">
            <h3 class="font-bold text-navy-900 mb-4">Tambah Meja</h3>
            <form action="{{ route('admin.towers.tables.store', $tower) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Nomor Meja</label>
                    <input type="text" name="table_number" class="w-full px-4 py-2 rounded-lg border border-gray-300 focus:ring-2 focus:ring-orange-500/20 focus:border-orange-500" placeholder="101" required>
                </div>
                <button type="submit" class="w-full py-2 bg-navy-900 text-white rounded-lg font-bold hover:bg-navy-800 transition">
                    + Simpan
                </button>
            </form>
        </div>
    </div>

    <!-- Rooms List -->
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Nomor Meja</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">QR Link</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($tables as $table)
                    <tr class="hover:bg-slate-50/50 transition">
                        <td class="px-6 py-4 font-bold text-navy-900">{{ $table->table_number }}</td>
                        <td class="px-6 py-4 text-sm">
                            <a href="{{ route('admin.tables.qr', $table) }}" target="_blank" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium">
                                <i class="fas fa-qrcode"></i> Generate QR
                            </a>
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('admin.tables.destroy', $table) }}" method="POST" class="inline" onsubmit="return confirm('Hapus meja ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-500 hover:text-red-700">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-8 text-center text-gray-500 italic">Belum ada meja terdaftar</td>
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
