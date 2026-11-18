@extends('layouts.app')

@section('title', 'Riwayat Aktivitas & Audit Log')
@section('page-title', 'Riwayat Aktivitas Sistem')

@section('content')
<div class="space-y-6">
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <i data-lucide="activity" class="w-6 h-6 text-emerald-600"></i>
                    Audit Trail & Log Aktivitas Sistem
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Seluruh rekaman tindakan pengguna, perubahan data stok, transaksi kasir, dan pembelian dicatat secara transparan.
                </p>
            </div>
        </div>

        <!-- Filter Form -->
        <form action="{{ route('aktivitas.index') }}" method="GET" class="mt-6 pt-6 border-t border-gray-100 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-3">
            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="{{ $startDate }}"
                       class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="{{ $endDate }}"
                       class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Filter Operator</label>
                <select name="pengguna_id" class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
                    <option value="">Semua Operator</option>
                    @foreach($penggunas as $p)
                        <option value="{{ $p->id }}" {{ $penggunaId == $p->id ? 'selected' : '' }}>
                            {{ $p->nama }} ({{ ucfirst($p->peran) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-gray-600 mb-1">Jenis Aksi</label>
                <input type="text" name="aksi" value="{{ $aksi }}" placeholder="cth: penjualan, stok..."
                       class="w-full text-xs rounded-lg border-gray-300 focus:border-emerald-500 focus:ring-emerald-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition-colors">
                    Filter Log
                </button>
                @if($startDate || $endDate || $penggunaId || $aksi)
                    <a href="{{ route('aktivitas.index') }}" class="py-2 px-3 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-lg text-xs font-medium transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Activity Log List -->
    <div class="bg-white rounded-xl shadow-xs border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-gray-600">
                <thead class="bg-gray-50 text-xs font-semibold uppercase text-gray-700 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-3.5">Waktu Kejadian</th>
                        <th class="px-6 py-3.5">Operator</th>
                        <th class="px-6 py-3.5">Aksi / Modul</th>
                        <th class="px-6 py-3.5">Deskripsi Aktivitas</th>
                        <th class="px-6 py-3.5 text-right">Alamat IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($aktivitas as $log)
                    <tr class="hover:bg-gray-50/50">
                        <td class="px-6 py-4 text-xs font-mono text-gray-500 whitespace-nowrap">
                            {{ $log->dibuat_pada ? $log->dibuat_pada->format('d/m/Y H:i:s') : '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-medium text-gray-900">{{ $log->pengguna?->nama ?? 'Sistem Otomatis' }}</div>
                            @if($log->pengguna)
                                <div class="text-[11px] text-gray-400 capitalize">{{ $log->pengguna->peran }}</div>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold
                                {{ str_contains($log->aksi, 'hapus') || str_contains($log->aksi, 'batal') ? 'bg-rose-100 text-rose-800' :
                                   (str_contains($log->aksi, 'tambah') || str_contains($log->aksi, 'selesai') ? 'bg-emerald-100 text-emerald-800' : 'bg-blue-100 text-blue-800') }}">
                                {{ $log->aksi }}
                            </span>
                            @if($log->tabel_terkait)
                                <span class="text-[10px] text-gray-400 font-mono block mt-0.5">{{ $log->tabel_terkait }} #{{ $log->id_referensi }}</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-xs text-gray-700 max-w-md">
                            {{ $log->deskripsi }}
                        </td>
                        <td class="px-6 py-4 text-xs font-mono text-gray-400 text-right whitespace-nowrap">
                            {{ $log->ip_address ?? '127.0.0.1' }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada riwayat aktivitas yang tercatat sesuai filter.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($aktivitas->hasPages())
            <div class="p-4 border-t border-gray-100">
                {{ $aktivitas->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
