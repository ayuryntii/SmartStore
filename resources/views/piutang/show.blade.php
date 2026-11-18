@extends('layouts.app', ['title' => 'Buku Utang Pelanggan', 'breadcrumb' => 'Utang > ' . $pelanggan->nama_pelanggan])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Buku Utang: {{ $pelanggan->nama_pelanggan }}</h2>
            <p class="text-xs text-slate-500">Nomor Telepon: {{ $pelanggan->nomor_telepon ?? '-' }} &bull; Alamat: {{ $pelanggan->alamat ?? '-' }}</p>
        </div>
        <div class="flex items-center gap-2">
            @if($totalUtang > 0)
                <button type="button" onclick="openPelunasanModal()"
                    class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                    <i data-lucide="wallet" class="w-4 h-4"></i>
                    <span>Pelunasan Utang Pelanggan</span>
                </button>
            @endif
            <a href="{{ route('piutang.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
                <i data-lucide="arrow-left" class="w-4 h-4"></i>
                <span>Kembali</span>
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Saldo Utang Belum Lunas</span>
            <span class="text-2xl font-black text-rose-600">Rp {{ number_format($totalUtang, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Akumulasi seluruh faktur belum selesai</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Pembayaran Telah Diterima</span>
            <span class="text-2xl font-black text-emerald-600">Rp {{ number_format($totalDibayar, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Total akumulasi setoran</span>
        </div>
    </div>

    <!-- Invoices Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Daftar Faktur Pembelian Konsumen
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">No Faktur</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4 text-right">Tagihan</th>
                        <th class="py-3 px-4 text-right">Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Utang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($sales as $sale)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('penjualan.show', $sale->id) }}" class="hover:text-brand-700">
                                    {{ $sale->nomor_faktur }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $sale->tanggal_penjualan->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                {{ $sale->tanggal_jatuh_tempo ? $sale->tanggal_jatuh_tempo->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right text-slate-600 font-bold">
                                Rp {{ number_format($sale->total_tagihan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right text-emerald-700 font-bold">
                                Rp {{ number_format($sale->total_bayar, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-black text-rose-600">
                                @if($sale->sisa_tagihan > 0)
                                    Rp {{ number_format($sale->sisa_tagihan, 0, ',', '.') }}
                                @else
                                    <span class="text-slate-400 font-normal">-</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($sale->status_pembayaran === 'lunas')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Belum Lunas</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('penjualan.show', $sale->id) }}" class="px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-400">Pelanggan ini belum memiliki riwayat transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Pelunasan Utang Pelanggan -->
<div id="modalPelunasan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-1">Pelunasan Utang Pelanggan</h3>
        <p class="text-xs text-slate-400 mb-4">{{ $pelanggan->nama_pelanggan }}</p>
        <form action="{{ route('piutang.bayar-pelanggan', $pelanggan->id) }}" method="POST">
            @csrf
            <div class="space-y-4">
                <div class="p-3 rounded-2xl bg-rose-50 border border-rose-200 text-xs flex justify-between items-center text-rose-900">
                    <span class="font-semibold">Total Saldo Utang:</span>
                    <span class="font-black text-base">Rp {{ number_format($totalUtang, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nominal Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_bayar" required min="1" max="{{ $totalUtang }}"
                        value="{{ $totalUtang }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-brand-600">
                    <p class="mt-1 text-[11px] text-slate-400">*Sistem akan mengalokasikan pembayaran ke faktur tertua terlebih dahulu.</p>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Metode Pembayaran</label>
                    <select name="metode_pembayaran" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                        <option value="tunai">Tunai</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="qris">QRIS</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan pelunasan..."
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closePelunasanModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-md">Simpan Pelunasan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPelunasanModal() {
        document.getElementById('modalPelunasan').classList.remove('hidden');
    }
    function closePelunasanModal() {
        document.getElementById('modalPelunasan').classList.add('hidden');
    }
</script>
@endsection
