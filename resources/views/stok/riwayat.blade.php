@extends('layouts.app', ['title' => 'Riwayat Pergerakan Stok', 'breadcrumb' => 'Stok > Riwayat Pergerakan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Riwayat Pergerakan Stok</h2>
            <p class="text-xs text-slate-500">Log lengkap seluruh transaksi keluar masuk, penyesuaian, dan retur barang</p>
        </div>
        <a href="{{ route('stok.penyesuaian') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md transition">
            <i data-lucide="plus" class="w-4 h-4"></i>
            <span>Penyesuaian Stok</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('stok.riwayat') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
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
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Jenis Pergerakan</label>
                <select name="jenis_pergerakan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Jenis</option>
                    <option value="pembelian_masuk" {{ $jenisPergerakan === 'pembelian_masuk' ? 'selected' : '' }}>Pembelian Masuk</option>
                    <option value="penjualan_keluar" {{ $jenisPergerakan === 'penjualan_keluar' ? 'selected' : '' }}>Penjualan Keluar</option>
                    <option value="penyesuaian_masuk" {{ $jenisPergerakan === 'penyesuaian_masuk' ? 'selected' : '' }}>Penyesuaian Masuk</option>
                    <option value="penyesuaian_keluar" {{ $jenisPergerakan === 'penyesuaian_keluar' ? 'selected' : '' }}>Penyesuaian Keluar</option>
                    <option value="retur_masuk" {{ $jenisPergerakan === 'retur_masuk' ? 'selected' : '' }}>Retur Masuk</option>
                    <option value="retur_keluar" {{ $jenisPergerakan === 'retur_keluar' ? 'selected' : '' }}>Retur Keluar</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Pilih Barang</label>
                <select name="barang_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Barang</option>
                    @foreach($barangList as $b)
                        <option value="{{ $b->id }}" {{ $barangId == $b->id ? 'selected' : '' }}>
                            {{ $b->nama_barang }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('stok.riwayat') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
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
                        <th class="py-3 px-4">Waktu</th>
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4">Jenis Pergerakan</th>
                        <th class="py-3 px-4 text-right">Jumlah</th>
                        <th class="py-3 px-4">Batch ID</th>
                        <th class="py-3 px-4">Petugas</th>
                        <th class="py-3 px-4">Keterangan / Referensi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($riwayat as $row)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $row->tanggal_pergerakan->format('d/m/Y H:i') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $row->barang?->nama_barang ?? '-' }}
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold
                                    {{ str_contains($row->jenis_pergerakan, 'masuk') ? 'bg-emerald-100 text-emerald-800' : 'bg-rose-100 text-rose-800' }}">
                                    {{ $row->jenis_pergerakan_label }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right font-extrabold text-sm
                                {{ str_contains($row->jenis_pergerakan, 'masuk') ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ str_contains($row->jenis_pergerakan, 'masuk') ? '+' : '-' }}{{ number_format($row->jumlah, 0, ',', '.') }}
                                <span class="text-[10px] font-normal text-slate-400">{{ $row->barang?->satuan?->singkatan }}</span>
                            </td>
                            <td class="py-3 px-4 font-mono text-slate-500 text-[11px]">
                                {{ $row->batch?->kode_batch ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-600">
                                {{ $row->pengguna?->nama ?? 'Sistem' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $row->catatan ?? '-' }}
                                @if($row->jenis_referensi)
                                    <span class="text-[10px] text-slate-400 block font-mono">Ref: {{ $row->jenis_referensi }} #{{ $row->referensi_id }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Tidak ada riwayat pergerakan stok.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($riwayat->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $riwayat->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
