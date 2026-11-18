@extends('layouts.app', ['title' => 'Pencatatan Utang Pelanggan', 'breadcrumb' => 'Keuangan > Utang Pelanggan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Pencatatan Utang & Piutang Pelanggan</h2>
            <p class="text-xs text-slate-500">Kelola buku utang konsumen, pantau jatuh tempo, dan catat cicilan pelunasan tagihan</p>
        </div>
    </div>

    <!-- Summary Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Total Saldo Piutang</span>
            <span class="text-2xl font-black text-rose-600">Rp {{ number_format($totalSemuaPiutang, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Belum dilunasi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Piutang Jatuh Tempo</span>
            <span class="text-2xl font-black text-rose-700">Rp {{ number_format($totalJatuhTempo, 0, ',', '.') }}</span>
            <span class="text-[11px] text-rose-600 block mt-1">Lewat batas waktu</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Faktur Belum Lunas</span>
            <span class="text-2xl font-black text-amber-600">{{ number_format($totalBelumLunasCount, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Transaksi</span>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
            <span class="text-xs text-slate-400 font-semibold block mb-1">Faktur Lunas</span>
            <span class="text-2xl font-black text-emerald-600">{{ number_format($totalLunasCount, 0, ',', '.') }}</span>
            <span class="text-[11px] text-slate-400 block mt-1">Selesai terbayar</span>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm">
        <form method="GET" action="{{ route('piutang.index') }}" class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Pilih Pelanggan</label>
                <select name="pelanggan_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Pelanggan</option>
                    @foreach($pelanggans as $p)
                        <option value="{{ $p->id }}" {{ $pelangganId == $p->id ? 'selected' : '' }}>{{ $p->nama_pelanggan }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-bold text-slate-500 mb-1">Status Pembayaran</label>
                <select name="status_pembayaran" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-brand-600">
                    <option value="">Semua Status</option>
                    <option value="belum_dibayar" {{ $statusFilter === 'belum_dibayar' ? 'selected' : '' }}>Belum Dibayar Sama Sekali</option>
                    <option value="dibayar_sebagian" {{ $statusFilter === 'dibayar_sebagian' ? 'selected' : '' }}>Dibayar Sebagian (Cicilan)</option>
                    <option value="jatuh_tempo" {{ $statusFilter === 'jatuh_tempo' ? 'selected' : '' }}>Jatuh Tempo (Terlambat)</option>
                    <option value="lunas" {{ $statusFilter === 'lunas' ? 'selected' : '' }}>Lunas</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-slate-900 text-white font-bold text-xs hover:bg-slate-800 transition">
                    Filter
                </button>
                <a href="{{ route('piutang.index') }}" class="py-2 px-3 rounded-xl border border-slate-300 text-slate-600 font-semibold text-xs hover:bg-slate-100 transition">
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
                        <th class="py-3 px-4">No Faktur</th>
                        <th class="py-3 px-4">Tanggal Penjualan</th>
                        <th class="py-3 px-4">Pelanggan</th>
                        <th class="py-3 px-4">Jatuh Tempo</th>
                        <th class="py-3 px-4 text-right">Total Tagihan</th>
                        <th class="py-3 px-4 text-right">Sudah Dibayar</th>
                        <th class="py-3 px-4 text-right">Sisa Utang</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($piutangs as $sale)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('penjualan.show', $sale->id) }}" class="hover:text-brand-700">
                                    {{ $sale->nomor_faktur }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                                {{ $sale->tanggal_penjualan->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($sale->pelanggan)
                                    <a href="{{ route('piutang.show', $sale->pelanggan->id) }}" class="font-bold text-slate-900 hover:text-brand-700">
                                        {{ $sale->pelanggan->nama_pelanggan }}
                                    </a>
                                @else
                                    <span class="text-slate-500 font-bold">Pelanggan Umum</span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($sale->tanggal_jatuh_tempo)
                                    <span class="text-slate-700 font-mono text-[11px] block">{{ $sale->tanggal_jatuh_tempo->format('d/m/Y') }}</span>
                                    @if($sale->status_pembayaran === 'jatuh_tempo')
                                        <span class="text-[10px] font-bold text-rose-600 block">Lewat Jatuh Tempo</span>
                                    @endif
                                @else
                                    <span class="text-slate-400">-</span>
                                @endif
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
                                @elseif($sale->status_pembayaran === 'dibayar_sebagian')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Sebagian</span>
                                @elseif($sale->status_pembayaran === 'jatuh_tempo')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-rose-100 text-rose-800">Jatuh Tempo</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-orange-100 text-orange-800">Belum Bayar</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-1.5">
                                    @if($sale->sisa_tagihan > 0)
                                        <button type="button"
                                            onclick="openBayarModal('{{ $sale->id }}', '{{ $sale->nomor_faktur }}', '{{ $sale->sisa_tagihan }}')"
                                            class="px-2.5 py-1 rounded-lg text-[11px] font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-sm transition">
                                            Bayar
                                        </button>
                                    @endif
                                    <a href="{{ route('penjualan.show', $sale->id) }}" class="p-1 rounded text-slate-400 hover:text-slate-700">
                                        <i data-lucide="eye" class="w-4 h-4"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-400">Tidak ada catatan utang yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($piutangs->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $piutangs->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Bayar Utang -->
<div id="modalBayar" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 class="text-base font-bold text-slate-900 mb-1">Catat Pembayaran Utang</h3>
        <p class="text-xs text-slate-400 mb-4" id="bayarFakturLabel">Faktur: -</p>
        <form action="{{ route('piutang.bayar') }}" method="POST">
            @csrf
            <input type="hidden" id="bayarPenjualanId" name="penjualan_id" value="">

            <div class="space-y-4">
                <div class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-xs flex justify-between">
                    <span class="text-slate-500 font-semibold">Sisa Utang Faktur Ini:</span>
                    <span id="bayarSisaDisplay" class="font-black text-rose-600">Rp 0</span>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Jumlah Bayar (Rp) <span class="text-rose-500">*</span></label>
                    <input type="number" id="bayarJumlah" name="jumlah_bayar" required min="1"
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
                        class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closeBayarModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-md">Simpan Pembayaran</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openBayarModal(id, faktur, sisa) {
        document.getElementById('bayarPenjualanId').value = id;
        document.getElementById('bayarFakturLabel').textContent = 'Faktur: ' + faktur;
        document.getElementById('bayarSisaDisplay').textContent = 'Rp ' + Number(sisa).toLocaleString('id-ID');
        const input = document.getElementById('bayarJumlah');
        input.max = sisa;
        input.value = sisa;
        document.getElementById('modalBayar').classList.remove('hidden');
    }
    function closeBayarModal() {
        document.getElementById('modalBayar').classList.add('hidden');
    }
</script>
@endsection
