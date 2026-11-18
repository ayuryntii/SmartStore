@extends('layouts.app', ['title' => 'Detail Pelanggan', 'breadcrumb' => 'Pelanggan > ' . $pelanggan->nama_pelanggan])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $pelanggan->nama_pelanggan }}</h2>
            <p class="text-xs text-slate-500 font-mono">Telp: {{ $pelanggan->nomor_telepon ?? '-' }} &bull; Alamat: {{ $pelanggan->alamat ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('piutang.show', $pelanggan->id) }}" class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i data-lucide="receipt" class="w-4 h-4"></i>
                <span>Buku Utang</span>
            </a>
            <a href="{{ route('pelanggan.edit', $pelanggan->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Ubah</span>
            </a>
            <a href="{{ route('pelanggan.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Utang Aktif Saat Ini</span>
            <span class="text-2xl font-black text-rose-600">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Belanjaan Terbayar</span>
            <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalBelanja, 0, ',', '.') }}</span>
        </div>
    </div>

    <!-- Sales history -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Riwayat Pembelian Pelanggan
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">No Faktur</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-right">Tagihan</th>
                        <th class="py-3 px-4 text-right">Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Utang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pelanggan->penjualan as $pj)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $pj->nomor_faktur }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $pj->tanggal_penjualan->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-right text-slate-700 font-bold">Rp {{ number_format($pj->total_tagihan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right text-emerald-700 font-bold">Rp {{ number_format($pj->total_bayar, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-black text-rose-600">
                                @if($pj->sisa_tagihan > 0)
                                    Rp {{ number_format($pj->sisa_tagihan, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($pj->status_pembayaran === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('penjualan.show', $pj->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-6 text-center text-slate-400">Belum ada riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
