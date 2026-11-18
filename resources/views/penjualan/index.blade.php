@extends('layouts.app', ['title' => 'Penjualan (Kasir)', 'breadcrumb' => 'Transaksi > Penjualan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Transaksi Penjualan & Kasir</h2>
            <p class="text-xs text-slate-500">Daftar riwayat transaksi penjualan, cetak struk kasir, dan status pembayaran</p>
        </div>
        <a href="{{ route('penjualan.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i>
            <span>Transaksi Kasir Baru</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('penjualan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari No Faktur</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="PJ-..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Pelanggan</label>
                <select name="pelanggan_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Pelanggan</option>
                    @foreach($pelanggans as $p)
                        <option value="{{ $p->id }}" {{ $pelangganId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama_pelanggan }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('penjualan.index') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
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
                        <th class="py-3 px-4">No Faktur</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Kasir</th>
                        <th class="py-3 px-4 text-right">Total Tagihan</th>
                        <th class="py-3 px-4 text-right">Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Utang</th>
                        <th class="py-3 px-4 text-center">Status Bayar</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($penjualan as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('penjualan.show', $item->id) }}" class="hover:text-brand-700">
                                    {{ $item->nomor_faktur }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $item->tanggal_penjualan->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-800">
                                {{ $item->pelanggan?->nama_pelanggan ?? 'Umum' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $item->pengguna?->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-bold text-slate-900">
                                Rp {{ number_format($item->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-emerald-700 font-bold">
                                Rp {{ number_format($item->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-rose-600">
                                @if($item->sisa_tagihan > 0)
                                    Rp {{ number_format($item->sisa_tagihan, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status_penjualan === 'dibatalkan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700">
                                        Dibatalkan
                                    </span>
                                @elseif($item->status_pembayaran === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                        Lunas
                                    </span>
                                @elseif($item->status_pembayaran === 'dibayar_sebagian')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">
                                        Sebagian
                                    </span>
                                @elseif($item->status_pembayaran === 'jatuh_tempo')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">
                                        Jatuh Tempo
                                    </span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-100 text-orange-800">
                                        Belum Bayar
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    <a href="{{ route('penjualan.show', $item->id) }}" title="Lihat Detail"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-slate-900 hover:bg-slate-100">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                    <a href="{{ route('penjualan.print', $item->id) }}" target="_blank" title="Cetak Struk"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-brand-700 hover:bg-slate-100">
                                        <i data-lucide="printer" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Belum ada transaksi penjualan yang sesuai.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($penjualan->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $penjualan->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
