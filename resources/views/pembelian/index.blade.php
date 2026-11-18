@extends('layouts.app', ['title' => 'Pembelian Barang', 'breadcrumb' => 'Pengadaan > Pembelian'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pembelian & Kulakan Barang</h2>
            <p class="text-xs text-slate-500">Pencatatan faktur barang masuk dari pemasok/distributor, pencatatan batch & kedaluwarsa</p>
        </div>
        <a href="{{ route('pembelian.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Catat Pembelian Baru</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('pembelian.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Cari No Faktur</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="PB-..."
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Pemasok</label>
                <select name="pemasok_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Pemasok</option>
                    @foreach($pemasoks as $p)
                        <option value="{{ $p->id }}" {{ $pemasokId == $p->id ? 'selected' : '' }}>{{ $p->nama_pemasok }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Status</label>
                <select name="status_pembelian" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Status</option>
                    <option value="diterima" {{ $statusPembelian === 'diterima' ? 'selected' : '' }}>Diterima (Stok Masuk)</option>
                    <option value="draf" {{ $statusPembelian === 'draf' ? 'selected' : '' }}>Draf (Menunggu)</option>
                    <option value="dibatalkan" {{ $statusPembelian === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                    class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('pembelian.index') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
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
                        <th class="py-3 px-4">Tanggal Pembelian</th>
                        <th class="py-3 px-4">Pemasok</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4 text-right">Total Pembelian</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pembelian as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('pembelian.show', $item->id) }}" class="hover:text-brand-700">
                                    {{ $item->nomor_faktur }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $item->tanggal_pembelian->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $item->pemasok?->nama_pemasok ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $item->pengguna?->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($item->total_pembelian, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status_pembelian === 'diterima')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Diterima</span>
                                @elseif($item->status_pembelian === 'draf')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Draf</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($item->status_pembelian === 'draf')
                                        <form action="{{ route('pembelian.receive', $item->id) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-white bg-emerald-700 hover:bg-emerald-800">
                                                Terima Barang
                                            </button>
                                        </form>
                                    @endif
                                    <a href="{{ route('pembelian.show', $item->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada transaksi pembelian.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($pembelian->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $pembelian->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
