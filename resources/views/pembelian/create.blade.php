@extends('layouts.app', ['title' => 'Catat Pembelian', 'breadcrumb' => 'Pembelian > Tambah'])

@section('content')
<div x-data="purchaseForm()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Catat Faktur Pembelian Barang</h2>
            <p class="text-xs text-slate-500">Pencatatan kulakan barang dari pemasok, stok masuk, kode batch, dan tanggal kedaluwarsa</p>
        </div>
        <a href="{{ route('pembelian.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('pembelian.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT 2 COLS: Item Rows -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="package-plus" class="w-4 h-4 text-brand-700"></i>
                            <span>Daftar Barang Pembelian</span>
                        </h3>
                        <button type="button" @click="addRow()" class="px-3 py-1.5 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100 text-xs font-bold transition flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah Baris</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200/70 space-y-3">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-slate-700" x-text="`Barang #${index + 1}`"></span>
                                    <button type="button" @click="removeRow(index)" x-show="rows.length > 1" class="text-rose-500 hover:text-rose-700 text-xs flex items-center gap-1">
                                        <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        <span>Hapus</span>
                                    </button>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div class="sm:col-span-2">
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Pilih Produk</label>
                                        <select :name="`items[${index}][barang_id]`" x-model="row.barang_id" @change="onItemChange(row)" required
                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900">
                                            <option value="">-- Pilih Barang --</option>
                                            <template x-for="b in availableItems" :key="b.id">
                                                <option :value="b.id" x-text="`${b.nama_barang} (${b.kode_barang})`"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Jumlah Kuantitas</label>
                                        <input type="number" step="0.001" min="0.001" required :name="`items[${index}][jumlah]`"
                                            x-model.number="row.jumlah"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold">
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Harga Beli Satuan (Rp)</label>
                                        <input type="number" step="0.01" min="0" required :name="`items[${index}][harga_satuan]`"
                                            x-model.number="row.harga_satuan"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Kode Batch (Opsional)</label>
                                        <input type="text" :name="`items[${index}][kode_batch]`" x-model="row.kode_batch" placeholder="BTH-..."
                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono">
                                    </div>
                                    <div>
                                        <label class="block text-[11px] font-semibold text-slate-600 mb-1">Tgl Kedaluwarsa</label>
                                        <input type="date" :name="`items[${index}][tanggal_kedaluwarsa]`" x-model="row.tanggal_kedaluwarsa"
                                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900">
                                    </div>
                                </div>

                                <div class="text-right text-xs pt-1 border-t border-slate-200">
                                    <span class="text-slate-400">Subtotal:</span>
                                    <span class="font-extrabold text-slate-900 ml-1">Rp <span x-text="Number(row.jumlah * row.harga_satuan).toLocaleString('id-ID')"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- RIGHT 1 COL: Supplier & Overall Info -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Info Faktur & Pemasok</h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Pemasok / Distributor <span class="text-rose-500">*</span></label>
                        <select name="pemasok_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                            <option value="">-- Pilih Pemasok --</option>
                            @foreach($pemasoks as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pemasok }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">No Faktur Pembelian</label>
                        <input type="text" name="nomor_faktur" value="{{ $suggestedInvoice }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 font-mono font-bold text-xs text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Pembelian</label>
                        <input type="datetime-local" name="tanggal_pembelian" value="{{ date('Y-m-d\TH:i') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Pembelian</label>
                        <select name="status_pembelian" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                            <option value="diterima" selected>Diterima (Langsung Masuk Stok & Batch)</option>
                            <option value="draf">Draf (Disimpan Dahulu)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan</label>
                        <textarea name="catatan" rows="2" placeholder="Catatan pembelian..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="pt-4 border-t border-slate-100">
                        <div class="flex justify-between items-center mb-4">
                            <span class="text-xs font-bold text-slate-600">Total Pembelian:</span>
                            <span class="text-xl font-black text-brand-700">Rp <span x-text="Number(total).toLocaleString('id-ID')"></span></span>
                        </div>

                        <button type="submit"
                            class="w-full py-3.5 px-4 rounded-2xl bg-brand-700 hover:bg-brand-800 text-white font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                            <i data-lucide="check" class="w-5 h-5"></i>
                            <span>Simpan Pembelian</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function purchaseForm() {
        return {
            availableItems: {!! json_encode($barangs) !!},
            rows: [
                { barang_id: '', jumlah: 1, harga_satuan: 0, kode_batch: '', tanggal_kedaluwarsa: '' }
            ],

            get total() {
                return this.rows.reduce((sum, r) => sum + (r.jumlah * r.harga_satuan), 0);
            },

            addRow() {
                this.rows.push({ barang_id: '', jumlah: 1, harga_satuan: 0, kode_batch: '', tanggal_kedaluwarsa: '' });
                this.$nextTick(() => lucide.createIcons());
            },

            removeRow(index) {
                if (this.rows.length > 1) {
                    this.rows.splice(index, 1);
                }
            },

            onItemChange(row) {
                const item = this.availableItems.find(b => b.id == row.barang_id);
                if (item) {
                    row.harga_satuan = Number(item.harga_beli || 0);
                }
            }
        };
    }
</script>
@endsection
