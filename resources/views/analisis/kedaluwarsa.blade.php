@extends('layouts.app', ['title' => 'Monitoring Kedaluwarsa (FEFO)', 'breadcrumb' => 'Analisis > Kedaluwarsa'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Monitoring Kedaluwarsa & Batch (FEFO)</h2>
            <p class="text-xs text-slate-500">First Expired First Out: Pantau tanggal kedaluwarsa barang untuk mencegah kerugian produk basi/rusak</p>
        </div>
    </div>

    <!-- Alert Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <a href="{{ route('analisis.kedaluwarsa', ['filter' => 'sudah_kedaluwarsa']) }}"
            class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-rose-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold">
                    <i data-lucide="skull" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Sudah Kedaluwarsa</span>
                    <span class="text-2xl font-black text-rose-600">{{ $totalSudahKedaluwarsa }} Batch</span>
                </div>
            </div>
            <span class="text-xs font-bold text-rose-600 bg-rose-50 px-2.5 py-1 rounded-full">Kritis</span>
        </a>

        <a href="{{ route('analisis.kedaluwarsa', ['filter' => '7_hari']) }}"
            class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-amber-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-amber-50 text-amber-600 flex items-center justify-center font-bold">
                    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Mendekati Expired (&le; 7 Hari)</span>
                    <span class="text-2xl font-black text-amber-600">{{ $totalKritis7Hari }} Batch</span>
                </div>
            </div>
            <span class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded-full">Perhatian</span>
        </a>

        <a href="{{ route('analisis.kedaluwarsa', ['filter' => '30_hari']) }}"
            class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex items-center justify-between hover:border-yellow-300 transition">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-yellow-50 text-yellow-600 flex items-center justify-center font-bold">
                    <i data-lucide="clock" class="w-5 h-5"></i>
                </div>
                <div>
                    <span class="text-xs text-slate-500 font-semibold block">Peringatan (&le; 30 Hari)</span>
                    <span class="text-2xl font-black text-yellow-700">{{ $totalPeringatan30Hari }} Batch</span>
                </div>
            </div>
            <span class="text-xs font-bold text-yellow-700 bg-yellow-50 px-2.5 py-1 rounded-full">Waspada</span>
        </a>
    </div>

    <!-- Filter Buttons -->
    <div class="flex items-center gap-2 overflow-x-auto text-xs font-bold">
        <a href="{{ route('analisis.kedaluwarsa') }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'semua' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200' }}">
            Semua Batch Aktif
        </a>
        <a href="{{ route('analisis.kedaluwarsa', ['filter' => 'sudah_kedaluwarsa']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $filter === 'sudah_kedaluwarsa' ? 'bg-rose-600 text-white border-rose-600' : 'bg-white text-slate-600 border-slate-200' }}">
            Sudah Kedaluwarsa
        </a>
        <a href="{{ route('analisis.kedaluwarsa', ['filter' => '7_hari']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $filter === '7_hari' ? 'bg-amber-600 text-white border-amber-600' : 'bg-white text-slate-600 border-slate-200' }}">
            Mendekati (7 Hari)
        </a>
        <a href="{{ route('analisis.kedaluwarsa', ['filter' => '30_hari']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $filter === '30_hari' ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-white text-slate-600 border-slate-200' }}">
            Peringatan (30 Hari)
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Kode Batch</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Pemasok</th>
                        <th class="py-3 px-4">Tanggal Kedaluwarsa</th>
                        <th class="py-3 px-4 text-center">Sisa Waktu</th>
                        <th class="py-3 px-4 text-right">Sisa Stok Batch</th>
                        <th class="py-3 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($batches as $b)
                        @php
                            $now = now()->startOfDay();
                            $exp = $b->tanggal_kedaluwarsa->copy()->startOfDay();
                            $daysDiff = (int) $now->diffInDays($exp, false);
                        @endphp
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">{{ $b->kode_batch ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                <a href="{{ route('barang.show', $b->barang_id) }}" class="hover:text-brand-700">
                                    {{ $b->barang?->nama_barang }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $b->detailPembelian?->pembelian?->pemasok?->nama_pemasok ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                {{ $b->tanggal_kedaluwarsa->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-center font-bold">
                                @if($daysDiff < 0)
                                    <span class="text-rose-600 font-bold">Terlewat {{ abs($daysDiff) }} Hari</span>
                                @elseif($daysDiff === 0)
                                    <span class="text-rose-600 font-extrabold">Hari Ini!</span>
                                @else
                                    <span class="text-slate-600">{{ $daysDiff }} Hari Lagi</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                {{ number_format($b->sisa_jumlah, 0, ',', '.') }} {{ $b->barang?->satuan?->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($daysDiff < 0)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Kedaluwarsa</span>
                                @elseif($daysDiff <= 7)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">&le; 7 Hari</span>
                                @elseif($daysDiff <= 30)
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-yellow-100 text-yellow-800">&le; 30 Hari</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada data batch barang yang sesuai kriteria.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
