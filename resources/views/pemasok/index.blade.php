@extends('layouts.app', ['title' => 'Daftar Pemasok', 'breadcrumb' => 'Pengadaan > Pemasok'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pemasok & Distributor</h2>
            <p class="text-xs text-slate-500">Kelola data mitra penyuplai barang dagangan warung</p>
        </div>
        <a href="{{ route('pemasok.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Pemasok Baru</span>
        </a>
    </div>

    <!-- Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm max-w-md">
        <form method="GET" action="{{ route('pemasok.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, telp, email..."
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
                        <th class="py-3 px-4">Nama Pemasok</th>
                        <th class="py-3 px-4">Nomor Telepon</th>
                        <th class="py-3 px-4">Alamat</th>
                        <th class="py-3 px-4">Email</th>
                        <th class="py-3 px-4 text-center">Transaksi Pembelian</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pemasok as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a href="{{ route('pemasok.show', $item->id) }}" class="hover:text-brand-700">
                                    {{ $item->nama_pemasok }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono">{{ $item->nomor_telepon ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->alamat ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->email ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $item->pembelian_count }} Transaksi
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('pemasok.show', $item->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('pemasok.edit', $item->id) }}" class="p-1 rounded text-slate-400 hover:text-brand-700">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </a>
                                    @if($item->pembelian_count == 0)
                                        <form action="{{ route('pemasok.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Hapus pemasok ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1 rounded text-slate-400 hover:text-rose-600">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-8 text-center text-slate-400">Belum ada data pemasok terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pemasok->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pemasok->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
