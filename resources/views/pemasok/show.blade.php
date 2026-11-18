@extends('layouts.app', ['title' => 'Detail Pemasok', 'breadcrumb' => 'Pemasok > ' . $pemasok->nama_pemasok])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $pemasok->nama_pemasok }}</h2>
            <p class="text-xs text-slate-500 font-mono">Telp: {{ $pemasok->nomor_telepon ?? '-' }} &bull; Email: {{ $pemasok->email ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('pembelian.create') }}" class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Catat Pembelian</span>
            </a>
            <a href="{{ route('pemasok.edit', $pemasok->id) }}" class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Ubah</span>
            </a>
            <a href="{{ route('pemasok.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Nilai Pembelian Masuk</span>
            <span class="text-2xl font-black text-brand-700">Rp {{ number_format($totalTransaksi, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Alamat Mitra</span>
            <span class="text-sm font-bold text-slate-800 block">{{ $pemasok->alamat ?? '-' }}</span>
        </div>
    </div>

    <!-- Purchases history -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Riwayat Pembelian dari Pemasok Ini
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">No Faktur</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4 text-right">Total Nilai</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($pemasok->pembelian as $pb)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $pb->nomor_faktur }}</td>
                            <td class="py-3 px-4 text-slate-500 font-mono">{{ $pb->tanggal_pembelian->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">Rp {{ number_format($pb->total_pembelian, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">
                                    {{ ucfirst($pb->status_pembelian) }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('pembelian.show', $pb->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Belum ada riwayat pembelian dari pemasok ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
