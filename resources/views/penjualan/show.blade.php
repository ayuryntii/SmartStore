@extends('layouts.app', ['title' => 'Detail Penjualan', 'breadcrumb' => 'Penjualan > ' . $penjualan->nomor_faktur])

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-3">
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Faktur {{ $penjualan->nomor_faktur }}</h2>
                @if($penjualan->status_penjualan === 'dibatalkan')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                @elseif($penjualan->status_pembayaran === 'lunas')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-emerald-100 text-emerald-800">Lunas</span>
                @elseif($penjualan->status_pembayaran === 'dibayar_sebagian')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-amber-100 text-amber-800">Sebagian</span>
                @elseif($penjualan->status_pembayaran === 'jatuh_tempo')
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-rose-100 text-rose-800">Jatuh Tempo</span>
                @else
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-extrabold bg-orange-100 text-orange-800">Belum Bayar</span>
                @endif
            </div>
            <p class="text-xs text-slate-500 font-mono">{{ $penjualan->tanggal_penjualan->format('d F Y, H:i') }} &bull; Kasir: {{ $penjualan->pengguna?->nama ?? '-' }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('penjualan.print', $penjualan->id) }}" target="_blank"
                class="px-4 py-2 rounded-xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak Struk</span>
            </a>

            @if($penjualan->sisa_tagihan > 0 && $penjualan->status_penjualan === 'selesai')
                <button type="button" onclick="openBayarModal('{{ $penjualan->id }}', '{{ $penjualan->nomor_faktur }}', '{{ $penjualan->sisa_tagihan }}')"
                    class="px-4 py-2 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
                    <i data-lucide="credit-card" class="w-4 h-4"></i>
                    <span>Bayar Utang</span>
                </button>
            @endif

            @if($penjualan->status_penjualan !== 'dibatalkan' && (auth()->user()->isAdmin() || auth()->user()->isKasir()))
                <button type="button" onclick="openBatalModal()"
                    class="px-3.5 py-2 rounded-xl border border-rose-200 text-rose-600 hover:bg-rose-50 text-xs font-bold transition flex items-center gap-1.5">
                    <i data-lucide="x-circle" class="w-4 h-4"></i>
                    <span>Batalkan Transaksi</span>
                </button>
            @endif
        </div>
    </div>

    <!-- Customer & Due Date Info -->
    <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm grid grid-cols-1 sm:grid-cols-3 gap-4 text-xs">
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Nama Pelanggan:</span>
            <span class="font-bold text-slate-900 text-sm block">{{ $penjualan->pelanggan?->nama_pelanggan ?? 'Pelanggan Umum' }}</span>
            <span class="text-slate-500 font-mono">{{ $penjualan->pelanggan?->nomor_telepon ?? '-' }}</span>
        </div>
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Tanggal Jatuh Tempo:</span>
            @if($penjualan->tanggal_jatuh_tempo)
                <span class="font-bold text-slate-900 block text-sm">{{ $penjualan->tanggal_jatuh_tempo->format('d F Y') }}</span>
                @if(now()->startOfDay()->gt($penjualan->tanggal_jatuh_tempo->startOfDay()) && $penjualan->sisa_tagihan > 0)
                    <span class="text-[10px] text-rose-600 font-bold">Terlambat {{ now()->diffInDays($penjualan->tanggal_jatuh_tempo) }} hari</span>
                @endif
            @else
                <span class="text-slate-400">-</span>
            @endif
        </div>
        <div>
            <span class="text-slate-400 block font-semibold mb-0.5">Catatan:</span>
            <span class="text-slate-700">{{ $penjualan->catatan ?? '-' }}</span>
        </div>
    </div>

    <!-- Items Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="p-4 border-b border-slate-100 font-bold text-slate-900 text-sm">
            Rincian Barang Terjual
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                        <th class="py-3 px-4">Nama Barang</th>
                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                        <th class="py-3 px-4 text-center">Kuantitas</th>
                        <th class="py-3 px-4 text-right">Subtotal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @foreach($penjualan->detailPenjualan as $det)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-bold text-slate-900">
                                {{ $det->barang?->nama_barang ?? '-' }}
                                <span class="text-[10px] text-slate-400 font-mono block">{{ $det->barang?->kode_barang }}</span>
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

        <!-- Totals summary block -->
        <div class="p-5 bg-slate-50/50 border-t border-slate-100 flex flex-col items-end text-xs space-y-1.5">
            <div class="w-64 flex justify-between text-slate-500">
                <span>Subtotal:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($penjualan->total_harga, 0, ',', '.') }}</span>
            </div>
            <div class="w-64 flex justify-between text-slate-500">
                <span>Diskon:</span>
                <span class="font-bold text-slate-800">Rp {{ number_format($penjualan->diskon, 0, ',', '.') }}</span>
            </div>
            <div class="w-64 flex justify-between text-sm font-black text-slate-900 pt-2 border-t border-slate-200">
                <span>Total Tagihan:</span>
                <span class="text-brand-700">Rp {{ number_format($penjualan->total_tagihan, 0, ',', '.') }}</span>
            </div>
            <div class="w-64 flex justify-between text-slate-600 font-bold">
                <span>Total Dibayar:</span>
                <span class="text-emerald-700">Rp {{ number_format($penjualan->total_bayar, 0, ',', '.') }}</span>
            </div>
            <div class="w-64 flex justify-between text-sm font-black pt-1 border-t border-slate-200">
                <span class="text-rose-600">Sisa Utang:</span>
                <span class="text-rose-600">Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }}</span>
            </div>
        </div>
    </div>

    <!-- Riwayat Pembayaran -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm p-5">
        <h3 class="text-sm font-bold text-slate-900 mb-3 flex items-center gap-2">
            <i data-lucide="history" class="w-4 h-4 text-brand-700"></i>
            <span>Histori Pembayaran Tagihan</span>
        </h3>
        <div class="space-y-2.5">
            @forelse($penjualan->pembayaranPenjualan as $bayar)
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                    <div>
                        <span class="font-bold text-slate-800">Metode: {{ ucfirst($bayar->metode_pembayaran) }}</span>
                        <span class="text-[11px] text-slate-400 block">{{ $bayar->catatan ?? 'Pembayaran kasir' }} &bull; Diterima oleh: {{ $bayar->pengguna?->nama ?? '-' }}</span>
                    </div>
                    <div class="text-right">
                        <span class="font-black text-sm text-emerald-700 block">Rp {{ number_format($bayar->jumlah_bayar, 0, ',', '.') }}</span>
                        <span class="text-[10px] text-slate-400 font-mono">{{ $bayar->tanggal_pembayaran->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-6 text-slate-400 text-xs">Belum ada riwayat pembayaran yang tercatat.</div>
            @endforelse
        </div>
    </div>
</div>

<!-- Modal Bayar Utang Faktur -->
<div id="modalBayar" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-4">Catat Pembayaran Utang</h3>
        <form action="{{ route('piutang.bayar') }}" method="POST">
            @csrf
            <input type="hidden" name="penjualan_id" value="{{ $penjualan->id }}">

            <div class="space-y-4">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex justify-between">
                    <span class="text-slate-500 font-semibold">Sisa Utang Faktur Ini:</span>
                    <span class="font-black text-rose-600">Rp {{ number_format($penjualan->sisa_tagihan, 0, ',', '.') }}</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Pembayaran (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" name="jumlah_bayar" required min="1" max="{{ $penjualan->sisa_tagihan }}"
                        value="{{ $penjualan->sisa_tagihan }}"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-slate-900 focus:ring-2 focus:ring-brand-600">
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
                    <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pembayaran</label>
                    <input type="datetime-local" name="tanggal_pembayaran" value="{{ date('Y-m-d\TH:i') }}" required
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                    <textarea name="catatan" rows="2" placeholder="Catatan pembayaran..."
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closeBayarModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-md">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Pembatalan -->
<div id="modalBatal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-rose-600 mb-2">Batalkan Transaksi Penjualan</h3>
        <p class="text-xs text-slate-500 mb-4">Stok barang yang sudah terpotong akan dikembalikan otomatis melalui riwayat retur masuk. Transaksi tidak akan dihapus.</p>
        <form action="{{ route('penjualan.cancel', $penjualan->id) }}" method="POST">
            @csrf
            <div>
                <label class="block text-xs font-bold text-slate-700 mb-1">Alasan Pembatalan <span class="text-rose-500">*</span></label>
                <textarea name="alasan" rows="3" required placeholder="Contoh: Kesalahan input kasir / pelanggan membatalkan pesanan"
                    class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-rose-600"></textarea>
            </div>
            <div class="flex items-center justify-end gap-2 mt-5">
                <button type="button" onclick="closeBatalModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-rose-600 hover:bg-rose-700 shadow-md">Konfirmasi Pembatalan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBayarModal() {
        document.getElementById('modalBayar').classList.remove('hidden');
    }
    function closeBayarModal() {
        document.getElementById('modalBayar').classList.add('hidden');
    }
    function openBatalModal() {
        document.getElementById('modalBatal').classList.remove('hidden');
    }
    function closeBatalModal() {
        document.getElementById('modalBatal').classList.add('hidden');
    }
</script>
@endsection
