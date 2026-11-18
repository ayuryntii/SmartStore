@extends('layouts.app', ['title' => 'Data Barang', 'breadcrumb' => 'Manajemen Data Barang'])

@section('content')
<div class="space-y-6">
    <!-- Header with Action -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">
                {{ auth()->user()->isAdmin() ? 'Katalog & Persediaan Barang' : 'Informasi & Katalog Barang' }}
            </h2>
            <p class="text-xs text-slate-500">
                {{ auth()->user()->isAdmin() ? 'Kelola master barang, harga beli/jual, dan batas stok minimum warung' : 'Cek ketersediaan stok fisik barang di rak, harga jual, dan kode internal barang' }}
            </p>
        </div>
        @if(auth()->user()->isAdmin())
        <div class="flex items-center gap-2">
            <a href="{{ route('barang.create') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Barang Baru</span>
            </a>
        </div>
        @endif
    </div>

    <!-- Filters and Search -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('barang.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari Nama / Kode Barang</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $search }}" placeholder="Ketik nama atau kode internal..."
                        class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                </div>
            </div>

            <!-- Kategori Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Kategori</label>
                <select name="kategori_id" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ $kategoriId == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama_kategori }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Status Stok Filter -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Status Stok</label>
                <select name="status_stok" class="w-full py-2 px-3 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $statusStok === 'aman' ? 'selected' : '' }}>Aman (&gt; Min)</option>
                    <option value="menipis" {{ $statusStok === 'menipis' ? 'selected' : '' }}>Menipis (&le; Min)</option>
                    <option value="habis" {{ $statusStok === 'habis' ? 'selected' : '' }}>Habis (0)</option>
                </select>
            </div>

            <!-- Buttons -->
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-4 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('barang.index') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Data Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50/80 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Harga Beli</th>
                        <th class="py-3 px-4 text-right">Harga Jual</th>
                        <th class="py-3 px-4 text-right">Stok Min</th>
                        <th class="py-3 px-4 text-right">Stok Sekarang</th>
                        <th class="py-3 px-4 text-center">Status Stok</th>
                        <th class="py-3 px-4 text-center">Aksi & Kontrol</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($barang as $item)
                        <tr class="hover:bg-slate-50/80 transition">
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">
                                {{ $item->kode_barang }}
                            </td>
                            <td class="py-3 px-4">
                                <a href="{{ route('barang.show', $item->id) }}" class="font-bold text-slate-900 hover:text-brand-700 block">
                                    {{ $item->nama_barang }}
                                </a>
                                @if($item->batchBarang && $item->batchBarang->first())
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5 flex items-center gap-1.5">
                                        <span class="px-1.5 py-0.2 rounded bg-slate-100 text-slate-600">Batch: {{ $item->batchBarang->first()->kode_batch }}</span>
                                        <span class="text-slate-500">Exp: {{ $item->batchBarang->first()->tanggal_kedaluwarsa?->format('d/m/Y') }}</span>
                                    </div>
                                @endif
                                @if(!$item->status_aktif)
                                    <span class="inline-block mt-0.5 text-[10px] text-rose-600 bg-rose-50 px-1.5 py-0.5 rounded font-bold">Nonaktif</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $item->kategori?->nama_kategori ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600">
                                Rp {{ number_format($item->harga_beli, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($item->harga_jual, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-500">
                                {{ number_format($item->stok_minimum, 0, ',', '.') }} <span class="text-[10px]">{{ $item->satuan?->singkatan }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-sm
                                {{ $item->status_stok === 'habis' ? 'text-rose-600' : '' }}
                                {{ $item->status_stok === 'menipis' ? 'text-amber-600' : '' }}
                                {{ $item->status_stok === 'aman' ? 'text-emerald-700' : '' }}">
                                {{ number_format($item->stok_saat_ini, 0, ',', '.') }} <span class="text-xs font-normal text-slate-500">{{ $item->satuan?->singkatan }}</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status_stok === 'habis')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                        Habis
                                    </span>
                                @elseif($item->status_stok === 'menipis')
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                        Menipis
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                                        Aman
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if(auth()->user()->isAdmin())
                                        <!-- TOMBOL LAPORKAN BARANG HABIS (ADMIN ONLY) -->
                                        <button type="button"
                                            onclick="openModalLaporHabis('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->stok_saat_ini }}')"
                                            title="Laporkan Barang Habis Fisik"
                                            class="px-2 py-1 rounded-lg text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200 transition flex items-center gap-1">
                                            <i data-lucide="alert-octagon" class="w-3.5 h-3.5 text-rose-600"></i>
                                            <span>Lapor Habis</span>
                                        </button>

                                        <!-- Detail Link -->
                                        <a href="{{ route('barang.show', $item->id) }}" title="Lihat Detail"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100 transition">
                                            <i data-lucide="eye" class="w-4 h-4"></i>
                                        </a>

                                        <!-- Edit Link -->
                                        <a href="{{ route('barang.edit', $item->id) }}" title="Ubah Data"
                                            class="p-1.5 rounded-lg text-slate-500 hover:text-brand-700 hover:bg-slate-100 transition">
                                            <i data-lucide="edit" class="w-4 h-4"></i>
                                        </a>
                                    @else
                                        <!-- KASIR ONLY: Lihat Informasi Barang (Read Only) -->
                                        <a href="{{ route('barang.show', $item->id) }}"
                                            class="inline-flex items-center gap-1 px-3 py-1 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold transition">
                                            <i data-lucide="eye" class="w-3.5 h-3.5 text-slate-500"></i>
                                            <span>Lihat Info</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-12 text-center text-slate-400">
                                <i data-lucide="inbox" class="w-10 h-10 mx-auto text-slate-300 mb-2"></i>
                                <p class="text-sm font-semibold">Tidak ada data barang yang sesuai kriteria pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($barang->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $barang->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
