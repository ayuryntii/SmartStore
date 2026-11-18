@extends('layouts.app', ['title' => 'Detail Barang', 'breadcrumb' => 'Data Barang > ' . $barang->nama_barang])

@section('content')
<div class="space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-2xl bg-brand-50 border border-brand-200 text-brand-700 flex items-center justify-center font-bold text-lg">
                <i data-lucide="package" class="w-6 h-6"></i>
            </div>
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $barang->nama_barang }}</h2>
                    @if($barang->status_stok === 'habis')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800 border border-rose-200">Habis</span>
                    @elseif($barang->status_stok === 'menipis')
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800 border border-amber-200">Menipis</span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">Aman</span>
                    @endif
                </div>
                <div class="text-xs text-slate-500 font-mono mt-1 flex flex-wrap items-center gap-2">
                    <span>Kode: <strong>{{ $barang->kode_barang }}</strong></span>
                    <span>&bull;</span>
                    <span>Kategori: <strong>{{ $barang->kategori?->nama_kategori }}</strong></span>
                    @if($barang->batchBarang->first())
                        <span>&bull;</span>
                        <span class="text-slate-700 font-bold">Batch: {{ $barang->batchBarang->first()->kode_batch }}</span>
                        <span>&bull;</span>
                        <span class="px-2 py-0.5 rounded-full text-[11px] font-bold bg-[#EBF3E5] text-[#344F23] border border-[#BFD5AE]">
                            Exp: {{ $barang->batchBarang->first()->tanggal_kedaluwarsa?->format('d/m/Y') }}
                        </span>
                    @endif
                </div>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('barang.index') }}"
                class="px-3.5 py-2 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>

            @if(auth()->user()->isAdmin())
                <!-- Tombol Laporkan Habis (Admin Only) -->
                <button type="button"
                    onclick="openModalLaporHabis('{{ $barang->id }}', '{{ addslashes($barang->nama_barang) }}', '{{ $barang->stok_saat_ini }}')"
                    class="px-3.5 py-2 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="alert-octagon" class="w-4 h-4 text-rose-600"></i>
                    <span>Laporkan Habis</span>
                </button>

                <!-- Edit Button -->
                <a href="{{ route('barang.edit', $barang->id) }}"
                    class="px-3.5 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    <span>Ubah</span>
                </a>

                <!-- Delete Form -->
                <form action="{{ route('barang.destroy', $barang->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus/menonaktifkan barang ini?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3.5 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition flex items-center gap-1.5">
                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Stok Saat Ini</span>
            <span class="text-2xl font-black text-slate-900">{{ number_format($barang->stok_saat_ini, 0, ',', '.') }}</span>
            <span class="text-xs text-slate-500 font-normal ml-1">{{ $barang->satuan?->singkatan }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Batas Minimum</span>
            <span class="text-2xl font-black text-slate-700">{{ number_format($barang->stok_minimum, 0, ',', '.') }}</span>
            <span class="text-xs text-slate-500 font-normal ml-1">{{ $barang->satuan?->singkatan }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Harga Beli</span>
            <span class="text-xl font-black text-slate-700">Rp {{ number_format($barang->harga_beli, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Harga Jual</span>
            <span class="text-xl font-black text-[#76944C]">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm col-span-2 sm:col-span-1">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Kedaluwarsa (FEFO)</span>
            @if($barang->batchBarang->first())
                <span class="text-sm font-black text-slate-900 block font-mono">
                    {{ $barang->batchBarang->first()->tanggal_kedaluwarsa?->format('d/m/Y') ?? 'Non-Batch' }}
                </span>
                <span class="text-[10px] text-slate-500 block truncate font-mono mt-0.5">
                    Batch: {{ $barang->batchBarang->first()->kode_batch }}
                </span>
            @else
                <span class="text-xs text-slate-400 italic">Non-Batch</span>
            @endif
        </div>
    </div>

    <!-- Active Batches (FEFO) -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold text-slate-900">Batch Stok & Kedaluwarsa Aktif (FEFO)</h3>
                <p class="text-xs text-slate-400">Pengurangan stok kasir memprioritaskan batch dengan kedaluwarsa terdekat</p>
            </div>
            <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600">
                {{ $barang->batchBarang->count() }} Batch Tersedia
            </span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-2.5 px-4">Kode Batch</th>
                        <th class="py-2.5 px-4">Tgl Kedaluwarsa</th>
                        <th class="py-2.5 px-4 text-right">Jumlah Diterima</th>
                        <th class="py-2.5 px-4 text-right">Sisa Jumlah</th>
                        <th class="py-2.5 px-4 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($barang->batchBarang as $batch)
                        <tr class="hover:bg-slate-50">
                            <td class="py-2.5 px-4 font-mono font-bold text-slate-800">{{ $batch->kode_batch ?? '-' }}</td>
                            <td class="py-2.5 px-4">
                                {{ $batch->tanggal_kedaluwarsa ? $batch->tanggal_kedaluwarsa->format('d/m/Y') : 'Tanpa Kedaluwarsa' }}
                            </td>
                            <td class="py-2.5 px-4 text-right">{{ number_format($batch->jumlah_diterima, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-right font-bold text-slate-900">{{ number_format($batch->sisa_jumlah, 0, ',', '.') }}</td>
                            <td class="py-2.5 px-4 text-center">
                                @if($batch->status_kedaluwarsa === 'kedaluwarsa')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800">Sudah Kedaluwarsa</span>
                                @elseif($batch->status_kedaluwarsa === 'kritis')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-100 text-amber-800">&le; 7 Hari</span>
                                @elseif($batch->status_kedaluwarsa === 'peringatan')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-yellow-100 text-yellow-800">&le; 30 Hari</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Aman</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-6 text-center text-slate-400">Tidak ada batch khusus. Stok tercatat dari penyesuaian non-batch.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if(auth()->user()->isAdmin())
    <!-- Riwayat Stok & Riwayat Kejadian Habis (Admin Only) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Riwayat Pergerakan Stok -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Riwayat Pergerakan Stok Terbaru</h3>
            <div class="space-y-3">
                @forelse($barang->riwayatStok as $log)
                    <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-slate-800 block">{{ $log->jenis_pergerakan_label }}</span>
                            <span class="text-[11px] text-slate-400">{{ $log->catatan }} &bull; Oleh: {{ $log->pengguna?->nama ?? 'Sistem' }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-black text-sm block {{ str_contains($log->jenis_pergerakan, 'masuk') ? 'text-emerald-600' : 'text-rose-600' }}">
                                {{ str_contains($log->jenis_pergerakan, 'masuk') ? '+' : '-' }}{{ number_format($log->jumlah, 0, ',', '.') }}
                            </span>
                            <span class="text-[10px] text-slate-400">{{ $log->tanggal_pergerakan->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Belum ada riwayat pergerakan stok.</div>
                @endforelse
            </div>
        </div>

        <!-- Riwayat Kejadian Habis -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
            <h3 class="text-sm font-bold text-slate-900 mb-3">Riwayat Kejadian Stok Habis</h3>
            <div class="space-y-3">
                @forelse($barang->riwayatStokHabis as $habis)
                    <div class="p-3 rounded-2xl bg-rose-50/50 border border-rose-100 flex items-center justify-between text-xs">
                        <div>
                            <div class="flex items-center gap-1.5 font-bold text-rose-800">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                <span>Mulai: {{ $habis->mulai_habis_pada->format('d/m/Y H:i') }}</span>
                            </div>
                            <span class="text-[11px] text-slate-500">{{ $habis->catatan }}</span>
                        </div>
                        <div class="text-right">
                            @if($habis->berakhir_pada)
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 block">Selesai</span>
                                <span class="text-[10px] text-slate-400">Durasi: {{ $habis->durasi_habis }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-100 text-rose-800 block">Masih Habis</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-6 text-slate-400 text-xs">Barang ini belum pernah mengalami kehabisan stok.</div>
                @endforelse
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
