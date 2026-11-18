@extends('layouts.app', ['title' => 'Analisis Barang Sering Habis', 'breadcrumb' => 'Analisis > Sering Habis'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Analisis Barang Paling Sering Habis</h2>
            <p class="text-xs text-slate-500">Menganalisis frekuensi stok kosong dan rata-rata durasi barang tidak tersedia</p>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('analisis.sering-habis') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Periode Analisis</label>
                <select name="periode" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:ring-2 focus:ring-brand-600">
                    <option value="semua" {{ $periode === 'semua' ? 'selected' : '' }}>Semua Data Historis</option>
                    <option value="harian" {{ $periode === 'harian' ? 'selected' : '' }}>7 Hari Terakhir</option>
                    <option value="mingguan" {{ $periode === 'mingguan' ? 'selected' : '' }}>4 Minggu Terakhir</option>
                    <option value="bulanan" {{ $periode === 'bulanan' ? 'selected' : '' }}>6 Bulan Terakhir</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Dari Tanggal (Kustom)</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800">
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Sampai Tanggal (Kustom)</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Terapkan
                </button>
                <a href="{{ route('analisis.sering-habis') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Chart -->
    <div class="bg-white p-6 rounded-3xl border border-slate-200/80 shadow-sm">
        <h3 class="text-sm font-bold text-slate-900 mb-1">Grafik 10 Barang Paling Sering Mengalami Stok Kosong</h3>
        <p class="text-xs text-slate-400 mb-4">Jumlah insiden stok mencapai nol pada periode yang dipilih</p>
        <div class="h-64">
            <canvas id="chartSeringHabis"></canvas>
        </div>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Tabel Frekuensi & Durasi Barang Habis
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">Peringkat</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4 text-center">Frekuensi Habis</th>
                        <th class="py-3 px-4 text-center">Rata-rata Durasi Kosong</th>
                        <th class="py-3 px-4 text-center">Terakhir Kali Habis</th>
                        <th class="py-3 px-4 text-right">Stok Saat Ini</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($analisis as $index => $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-400 text-center w-12">#{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $item['barang']?->nama_barang ?? '-' }}
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $item['barang']?->kode_barang }}</span>
                            </td>
                            <td class="py-3 px-4 text-slate-500">{{ $item['barang']?->kategori?->nama_kategori ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-black bg-rose-100 text-rose-800">
                                    {{ $item['frekuensi_habis'] }} Kali
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center font-bold text-slate-700">
                                {{ $item['rata_rata_durasi'] }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-500 font-mono text-[11px]">
                                {{ date('d/m/Y H:i', strtotime($item['terakhir_habis'])) }}
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-sm
                                {{ $item['stok_saat_ini'] <= 0 ? 'text-rose-600' : 'text-emerald-700' }}">
                                {{ number_format($item['stok_saat_ini'], 0, ',', '.') }} {{ $item['barang']?->satuan?->singkatan }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada riwayat kejadian stok habis yang tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('chartSeringHabis').getContext('2d');
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: {!! json_encode(!empty($chartLabels) ? $chartLabels : ['Belum Ada Data']) !!},
                datasets: [{
                    label: 'Frekuensi Kejadian Habis',
                    data: {!! json_encode(!empty($chartData) ? $chartData : [0]) !!},
                    backgroundColor: '#ef4444',
                    borderRadius: 8,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });
    });
</script>
@endsection
