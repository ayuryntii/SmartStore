@extends('layouts.app', ['title' => 'Manajemen Stok', 'breadcrumb' => 'Persediaan > Stok'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Manajemen & Monitoring Stok</h2>
            <p class="text-xs text-slate-500">Pantau tingkat ketersediaan fisik barang, batas minimum, dan lakukan penyesuaian stok</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('stok.penyesuaian') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
                <i data-lucide="sliders" class="w-4 h-4"></i>
                <span>Penyesuaian Stok Manual</span>
            </a>
            <a href="{{ route('stok.riwayat') }}"
                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-300 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold shadow-sm transition">
                <i data-lucide="history" class="w-4 h-4 text-slate-400"></i>
                <span>Riwayat Pergerakan</span>
            </a>
        </div>
    </div>

    <!-- Quick Stat Pills -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('stok.index', ['status_stok' => 'aman']) }}"
            class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-emerald-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold">
                    <i data-lucide="check-circle-2" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Stok Aman</span>
                    <span class="text-xl font-black text-slate-900">{{ number_format($totalAman, 0, ',', '.') }} Item</span>
                </div>
            </div>
            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full">&gt; Min</span>
        </a>

        <a href="{{ route('stok.index', ['status_stok' => 'menipis']) }}"
            class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-amber-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Stok Menipis</span>
                    <span class="text-xl font-black text-amber-600">{{ number_format($totalMenipis, 0, ',', '.') }} Item</span>
                </div>
            </div>
            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">&le; Min</span>
        </a>

        <a href="{{ route('stok.index', ['status_stok' => 'habis']) }}"
            class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-rose-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="x-circle" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Stok Habis</span>
                    <span class="text-xl font-black text-rose-600">{{ number_format($totalHabis, 0, ',', '.') }} Item</span>
                </div>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full">= 0</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('stok.index') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3">
            <div class="sm:col-span-2">
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama atau kode barang..."
                    class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
            </div>
            <div>
                <select name="status_stok" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Status Stok</option>
                    <option value="aman" {{ $statusStok === 'aman' ? 'selected' : '' }}>Aman</option>
                    <option value="menipis" {{ $statusStok === 'menipis' ? 'selected' : '' }}>Menipis</option>
                    <option value="habis" {{ $statusStok === 'habis' ? 'selected' : '' }}>Habis</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('stok.index') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Kode</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-right">Stok Minimum</th>
                        <th class="py-3 px-4 text-right">Stok Saat Ini</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($barang as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-700">{{ $item->kode_barang }}</td>
                            <td class="py-3 px-4">
                                <a href="{{ route('barang.show', $item->id) }}" class="font-bold text-slate-900 hover:text-brand-700">
                                    {{ $item->nama_barang }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $item->kategori?->nama_kategori }}</td>
                            <td class="py-3 px-4 text-right text-slate-500">{{ number_format($item->stok_minimum, 0, ',', '.') }} {{ $item->satuan?->singkatan }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-sm
                                {{ $item->status_stok === 'habis' ? 'text-rose-600' : '' }}
                                {{ $item->status_stok === 'menipis' ? 'text-amber-600' : '' }}
                                {{ $item->status_stok === 'aman' ? 'text-emerald-700' : '' }}">
                                {{ number_format($item->stok_saat_ini, 0, ',', '.') }} {{ $item->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status_stok === 'habis')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Habis</span>
                                @elseif($item->status_stok === 'menipis')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Menipis</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Aman</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <button type="button"
                                        onclick="openModalLaporHabis('{{ $item->id }}', '{{ addslashes($item->nama_barang) }}', '{{ $item->stok_saat_ini }}')"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-rose-700 bg-rose-50 hover:bg-rose-100 border border-rose-200">
                                        Lapor Habis
                                    </button>
                                    <a href="{{ route('barang.show', $item->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data stok barang.</td>
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
