@extends('layouts.app', ['title' => 'Daftar Pelanggan', 'breadcrumb' => 'Pelanggan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Data Pelanggan</h2>
            <p class="text-xs text-slate-500">Master data konsumen warung untuk kemudahan transaksi kasir dan catatan piutang</p>
        </div>
        <a href="{{ route('pelanggan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Pelanggan Baru</span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm max-w-md">
        <form method="GET" action="{{ route('pelanggan.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, telp, alamat..."
                class="flex-1 px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
            <button type="submit" class="px-4 py-2 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800">
                Cari
            </button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Nama Pelanggan</th>
                        <th class="py-3 px-4">Nomor Telepon</th>
                        <th class="py-3 px-4">Alamat</th>
                        <th class="py-3 px-4 text-center">Transaksi</th>
                        <th class="py-3 px-4 text-right">Saldo Utang Saat Ini</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pelanggan as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a href="{{ route('pelanggan.show', $item->id) }}" class="hover:text-brand-700">
                                    {{ $item->nama_pelanggan }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono">{{ $item->nomor_telepon ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->alamat ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $item->penjualan_count }} Transaksi
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-black text-rose-600">
                                @if($item->total_piutang > 0)
                                    Rp {{ number_format($item->total_piutang, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">Rp 0</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('piutang.show', $item->id) }}" title="Buku Utang" class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50">
                                        <i data-lucide="receipt" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('pelanggan.show', $item->id) }}" title="Detail" class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('pelanggan.edit', $item->id) }}" title="Ubah" class="p-1.5 rounded-lg text-slate-500 hover:text-brand-700 hover:bg-slate-100">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @if($item->penjualan_count == 0)
                                        <form action="{{ route('pelanggan.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pelanggan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada pelanggan terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pelanggan->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pelanggan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
