@extends('layouts.app')

@section('title', 'Manajemen Pengguna')
@section('page-title', 'Manajemen Pengguna Sistem')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="users" class="w-6 h-6 text-emerald-600"></i>
                    Daftar Pengguna Sistem (Hak Akses)
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Kelola akun operator, peran (Admin, Kasir, Gudang), dan status aktifitas akun di BAWARA.
                </p>
            </div>
            <div>
                <a href="{{ route('pengguna.create') }}"
                   class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-xs transition-colors">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    Tambah Pengguna
                </a>
            </div>
        </div>

        <!-- Filter -->
        <form action="{{ route('pengguna.index') }}" method="GET" class="mt-6 pt-6 border-t border-gray-100 flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[200px]">
                <div class="relative">
                    <i data-lucide="search" class="w-4 h-4 text-gray-400 absolute left-3 top-3"></i>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau email..."
                           class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                </div>
            </div>
            <div>
                <select name="peran" class="text-sm rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ $peran == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="kasir" {{ $peran == 'kasir' ? 'selected' : '' }}>Kasir</option>
                    <option value="gudang" {{ $peran == 'gudang' ? 'selected' : '' }}>Gudang</option>
                </select>
            </div>
            <button type="submit" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white text-sm font-medium rounded-lg transition-colors">
                Filter
            </button>
            @if($search || $peran)
                <a href="{{ route('pengguna.index') }}" class="px-3 py-2 text-sm text-gray-500 hover:text-gray-700">
                    Reset
                </a>
            @endif
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Nama Pengguna</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Peran (Role)</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4">Terdaftar</th>
                        <th class="px-6 py-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($pengguna as $u)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-sm">
                                    {{ strtoupper(substr($u->nama, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-900">{{ $u->nama }}</div>
                                    @if($u->id === auth()->id())
                                        <span class="inline-block px-1.5 py-0.5 bg-emerald-50 text-emerald-700 rounded text-[10px] font-medium border border-emerald-200">
                                            Akun Anda
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 font-mono text-xs text-gray-700">{{ $u->email }}</td>
                        <td class="px-6 py-4">
                            @if($u->peran === 'admin')
                                <span class="px-2.5 py-1 bg-purple-100 text-purple-800 rounded-full text-xs font-semibold flex items-center gap-1 w-max">
                                    <i data-lucide="shield" class="w-3 h-3"></i> Admin
                                </span>
                            @elseif($u->peran === 'kasir')
                                <span class="px-2.5 py-1 bg-blue-100 text-blue-800 rounded-full text-xs font-semibold flex items-center gap-1 w-max">
                                    <i data-lucide="shopping-cart" class="w-3 h-3"></i> Kasir
                                </span>
                            @else
                                <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-semibold flex items-center gap-1 w-max">
                                    <i data-lucide="warehouse" class="w-3 h-3"></i> Gudang
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if($u->status_aktif)
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Aktif
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-rose-100 text-rose-800">
                                    Nonaktif
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-500">
                            {{ $u->dibuat_pada ? $u->dibuat_pada->format('d/m/Y') : '-' }}
                        </td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('pengguna.edit', $u) }}"
                                   class="p-1.5 text-gray-500 hover:text-emerald-600 hover:bg-emerald-50 rounded-lg transition-colors" title="Edit Pengguna">
                                    <i data-lucide="edit-3" class="w-4 h-4"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                    <form action="{{ route('pengguna.destroy', $u) }}" method="POST"
                                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus akun {{ $u->nama }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-gray-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus Pengguna">
                                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada pengguna yang ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pengguna->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $pengguna->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
