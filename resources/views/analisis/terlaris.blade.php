@extends('layouts.app', ['title' => 'Analisis Barang Terlaris', 'breadcrumb' => 'Analisis > Terlaris & Kurang Laku'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Analisis Penjualan Barang</h2>
            <p class="text-xs text-slate-500">Evaluasi produk terlaris (fast moving) dan produk kurang laku (slow moving) untuk optimasi perputaran kas</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm max-w-xl">
        <form method="GET" action="{{ route('analisis.terlaris') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800">
                    Filter
                </button>
                <a href="{{ route('analisis.terlaris') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Chart Top Best Sellers -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Grafik 10 Produk Paling Banyak Terjual</h3>
        <p class="text-xs text-slate-400 mb-4">Total unit terjual pada transaksi berstatus selesai</p>
        <div class="h-64">
            <canvas id="chartTerlaris"></canvas>
        </div>
    </div>

    <!-- 2 Tables: Terlaris & Kurang Laku -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Tabel Terlaris -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-emerald-50/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="trending-up" class="w-4 h-4 text-emerald-700"></i>
                    <h4 class="text-sm font-bold text-slate-900">Produk Terlaris (Fast-Moving)</h4>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                            <th class="py-2.5 px-4">Nama Barang</th>
                            <th class="py-2.5 px-4 text-center">Unit Terjual</th>
                            <th class="py-2.5 px-4 text-right">Pendapatan</th>
                            <th class="py-2.5 px-4 text-right">Laba Kotor</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($terlaris->take(10) as $t)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $t['barang']?->nama_barang }}</td>
                                <td class="py-2.5 px-4 text-center font-extrabold text-emerald-700">
                                    {{ number_format($t['total_terjual'], 0, ',', '.') }} {{ $t['barang']?->satuan?->singkatan }}
                                </td>
                                <td class="py-2.5 px-4 text-right text-slate-700">Rp {{ number_format($t['total_pendapatan'], 0, ',', '.') }}</td>
                                <td class="py-2.5 px-4 text-right font-bold text-emerald-600">Rp {{ number_format($t['total_laba_kotor'], 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Kurang Laku -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-slate-100 bg-slate-100/50 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <i data-lucide="trending-down" class="w-4 h-4 text-slate-600"></i>
                    <h4 class="text-sm font-bold text-slate-900">Produk Kurang Laku (Slow-Moving)</h4>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                            <th class="py-2.5 px-4">Nama Barang</th>
                            <th class="py-2.5 px-4 text-center">Unit Terjual</th>
                            <th class="py-2.5 px-4 text-right">Stok Fisik Tersisa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @foreach($kurangLaku->take(10) as $k)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-4 font-bold text-slate-900">{{ $k['barang']?->nama_barang }}</td>
                                <td class="py-2.5 px-4 text-center font-bold text-slate-500">
                                    {{ number_format($k['total_terjual'], 0, ',', '.') }} {{ $k['barang']?->satuan?->singkatan }}
                                </td>
                                <td class="py-2.5 px-4 text-right font-bold text-amber-600">
                                    {{ number_format($k['barang']?->stok_saat_ini, 0, ',', '.') }} {{ $k['barang']?->satuan?->singkatan }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartTerlaris').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(!empty($topLabels) ? $topLabels : ['Belum Ada Penjualan']) !!},
                datasets: [{
                    label: 'Unit Terjual',
                    data: {!! json_encode(!empty($topQty) ? $topQty : [0]) !!},
                    backgroundColor: '#059669',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    });
</script>
@endsection
