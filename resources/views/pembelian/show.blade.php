@extends('layouts.app', ['title' => 'Detail Pembelian', 'breadcrumb' => 'Pembelian > ' . $pembelian->nomor_faktur])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Faktur {{ $pembelian->nomor_faktur }}</h2>
                @if($pembelian->status_pembelian === 'diterima')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Diterima</span>
                @elseif($pembelian->status_pembelian === 'draf')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">Draf</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-mono">{{ $pembelian->tanggal_pembelian->format('d F Y, H:i') }} &bull; Oleh: {{ $pembelian->pengguna?->nama ?? '-' }}</p>
        </div>

        <div class="flex items-center gap-2">
            @if($pembelian->status_pembelian === 'draf')
                <form action="{{ route('pembelian.receive', $pembelian->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                        <i data-lucide="check" class="w-4 h-4"></i>
                        <span>Terima Barang Sekarang</span>
                    </button>
                </form>
            @endif
            <a href="{{ route('pembelian.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Info Card -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Nama Pemasok:</span>
            <span class="font-bold text-slate-900 text-sm block">{{ $pembelian->pemasok?->nama_pemasok }}</span>
            <span class="text-slate-500">{{ $pembelian->pemasok?->nomor_telepon ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Status Barang:</span>
            @if($pembelian->status_pembelian === 'diterima')
                <span class="font-bold text-emerald-700 block">Stok Sudah Masuk Sistem</span>
            @else
                <span class="font-bold text-amber-600 block">Menunggu Penerimaan Fisik</span>
            @endif
        </div>
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Catatan Pembelian:</span>
            <span class="text-slate-700">{{ $pembelian->catatan ?? '-' }}</span>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Rincian Barang Masuk
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4 text-center">Batch / Kedaluwarsa</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-center">Jumlah</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($pembelian->detailPembelian as $det)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $det->barang?->nama_barang }}
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $det->barang?->kode_barang }}</span>
                            </td>
                            <td class="py-3 px-4 text-center font-mono">
                                @if($det->batchBarang)
                                    <span class="font-bold text-slate-700 block">{{ $det->batchBarang->kode_batch ?? '-' }}</span>
                                    <span class="text-[10px] text-slate-400">
                                        Exp: {{ $det->batchBarang->tanggal_kedaluwarsa ? $det->batchBarang->tanggal_kedaluwarsa->format('d/m/Y') : '-' }}
                                    </span>
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600">Rp {{ number_format($det->harga_satuan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center font-bold text-slate-800">
                                {{ number_format($det->jumlah, 0, ',', '.') }} {{ $det->barang?->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                Rp {{ number_format($det->subtotal, 0, ',', '.') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="p-5 bg-slate-50/50 border-t border-slate-100 flex justify-between items-center">
            <span class="text-xs font-bold text-slate-500">Total Nilai Pembelian:</span>
            <span class="text-xl font-black text-brand-700">Rp {{ number_format($pembelian->total_pembelian, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
