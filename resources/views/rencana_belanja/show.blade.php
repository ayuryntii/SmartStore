@extends('layouts.app', ['title' => 'Detail Rencana Belanja', 'breadcrumb' => 'Rencana Belanja > #RB-' . str_pad($rencanaBelanja->id, 4, '0', STR_PAD_LEFT)])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Rencana Belanja #RB-{{ str_pad($rencanaBelanja->id, 4, '0', STR_PAD_LEFT) }}</h2>
                @if($rencanaBelanja->status_rencana === 'selesai')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Selesai</span>
                @elseif($rencanaBelanja->status_rencana === 'dipesan')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-blue-100 text-blue-800">Dipesan</span>
                @elseif($rencanaBelanja->status_rencana === 'draf')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">Draf</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-mono">Tanggal Rencana: {{ $rencanaBelanja->tanggal_rencana->format('d F Y') }} &bull; Dibuat: {{ $rencanaBelanja->pengguna?->nama ?? '-' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Form ubah status rencana -->
            <form action="{{ route('rencana-belanja.update-status', $rencanaBelanja->id) }}" method="POST" class="flex items-center gap-1.5">
                @csrf
                @method('PATCH')
                <select name="status_rencana" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-700">
                    <option value="draf" {{ $rencanaBelanja->status_rencana === 'draf' ? 'selected' : '' }}>Draf</option>
                    <option value="dipesan" {{ $rencanaBelanja->status_rencana === 'dipesan' ? 'selected' : '' }}>Dipesan</option>
                    <option value="selesai" {{ $rencanaBelanja->status_rencana === 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ $rencanaBelanja->status_rencana === 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </form>

            <a href="{{ route('pembelian.create') }}" class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Catat Pembelian</span>
            </a>

            <a href="{{ route('rencana-belanja.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Catatan -->
    @if($rencanaBelanja->catatan)
        <div class="p-4 rounded-2xl bg-white border border-slate-200/80 shadow-sm text-xs text-slate-700">
            <span class="font-bold text-slate-400 block mb-0.5">Catatan:</span>
            {{ $rencanaBelanja->catatan }}
        </div>
    @endif

    <!-- Items Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Daftar Item Belanjaan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Stok Saat Ini</th>
                        <th class="py-3 px-4 text-center">Dibutuhkan</th>
                        <th class="py-3 px-4 text-center">Sudah Dibeli</th>
                        <th class="py-3 px-4 text-center">Status Item</th>
                        <th class="py-3 px-4 text-center">Aksi Cepat</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($rencanaBelanja->detailRencanaBelanja as $det)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $det->barang?->nama_barang }}
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $det->barang?->kode_barang }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $det->barang?->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ number_format($det->barang?->stok_saat_ini, 0, ',', '.') }} {{ $det->barang?->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center font-extrabold text-brand-700">
                                {{ number_format($det->jumlah_dibutuhkan, 0, ',', '.') }} {{ $det->barang?->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-emerald-600">
                                {{ number_format($det->jumlah_dibeli, 0, ',', '.') }} {{ $det->barang?->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($det->status_item === 'dibeli')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Dibeli</span>
                                @elseif($det->status_item === 'menunggu')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Menunggu</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($det->status_item !== 'dibeli')
                                        <form action="{{ route('rencana-belanja.update-item', $det->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_item" value="dibeli">
                                            <input type="hidden" name="jumlah_dibeli" value="{{ $det->jumlah_dibutuhkan }}">
                                            <button type="submit" class="px-2.5 py-1 rounded-lg text-[10px] font-bold text-white bg-emerald-600 hover:bg-emerald-700">
                                                Tandai Dibeli
                                            </button>
                                        </form>
                                    @endif
                                    @if($det->status_item !== 'dibatalkan')
                                        <form action="{{ route('rencana-belanja.update-item', $det->id) }}" method="POST">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="status_item" value="dibatalkan">
                                            <button type="submit" class="px-2 py-1 rounded-lg text-[10px] font-bold text-slate-600 hover:bg-slate-100">
                                                Batal
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Tidak ada item dalam rencana belanja ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
