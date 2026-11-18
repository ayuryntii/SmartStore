@extends('layouts.app', ['title' => 'Buat Rencana Belanja', 'breadcrumb' => 'Rencana Belanja > Buat'])

@section('content')
<div x-data="shoppingPlanForm()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Rencana Belanja & Rekomendasi Pengadaan</h2>
            <p class="text-xs text-slate-500">Sistem otomatis mendeteksi barang habis dan menipis sebagai rekomendasi kulakan</p>
        </div>
        <a href="{{ route('rencana-belanja.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali</span>
        </a>
    </div>

    <form action="{{ route('rencana-belanja.store') }}" method="POST">
        @csrf

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT 2 COLS: Item List -->
            <div class="lg:col-span-2 space-y-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                                <i data-lucide="clipboard-check" class="w-4 h-4 text-brand-700"></i>
                                <span>Daftar Barang yang Direncanakan</span>
                            </h3>
                            <p class="text-xs text-slate-400">Sesuaikan kuantitas yang dibutuhkan</p>
                        </div>
                        <button type="button" @click="addRow()" class="px-3 py-1.5 rounded-xl bg-brand-50 text-brand-700 hover:bg-brand-100 text-xs font-bold transition flex items-center gap-1">
                            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                            <span>Tambah Item Manual</span>
                        </button>
                    </div>

                    <div class="space-y-3">
                        <template x-for="(row, index) in rows" :key="index">
                            <div class="p-3.5 rounded-2xl bg-slate-50 border border-slate-200/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3 text-xs">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2">
                                        <select :name="`items[${index}][barang_id]`" x-model="row.barang_id" required
                                            class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-bold">
                                            <option value="">-- Pilih Barang --</option>
                                            <template x-for="b in allProducts" :key="b.id">
                                                <option :value="b.id" x-text="`${b.nama_barang} (Stok: ${b.stok_saat_ini})`"></option>
                                            </template>
                                        </select>
                                    </div>
                                    <span x-show="row.alasan" class="text-[10px] text-amber-700 font-semibold block mt-1" x-text="`Sebab: ${row.alasan}`"></span>
                                </div>

                                <div class="flex items-center gap-2 shrink-0">
                                    <label class="text-[11px] font-bold text-slate-500">Jumlah Butuh:</label>
                                    <input type="number" step="0.001" min="0.001" required :name="`items[${index}][jumlah_dibutuhkan]`"
                                        x-model.number="row.jumlah_dibutuhkan"
                                        class="w-24 px-2.5 py-1.5 rounded-xl border border-slate-300 text-xs font-bold text-slate-900 text-center">
                                    <button type="button" @click="removeRow(index)" class="p-1 text-slate-400 hover:text-rose-600">
                                        <i data-lucide="trash-2" class="w-4 h-4"></i>
                                    </button>
                                </div>
                            </div>
                        </template>

                        <div x-show="rows.length === 0" class="text-center py-8 text-slate-400">
                            <i data-lucide="check-circle" class="w-8 h-8 text-emerald-400 mx-auto mb-2"></i>
                            <p class="text-xs font-bold text-slate-700">Tidak ada stok yang terdeteksi menipis atau habis saat ini.</p>
                            <p class="text-[11px] text-slate-400">Klik "Tambah Item Manual" jika ingin merencanakan kulakan tambahan.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RIGHT 1 COL: Meta & Submit -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengaturan Rencana</h3>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Tanggal Rencana Kulakan <span class="text-rose-500">*</span></label>
                        <input type="date" name="tanggal_rencana" value="{{ date('Y-m-d') }}" required
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Status Rencana</label>
                        <select name="status_rencana" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900">
                            <option value="draf" selected>Draf (Daftar Pengingat Belanja)</option>
                            <option value="dipesan">Dipesan (Pesanan Sedang Dikirim)</option>
                            <option value="selesai">Selesai</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-1">Catatan Tambahan</label>
                        <textarea name="catatan" rows="3" placeholder="Catatan kulakan belanja..."
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                    </div>

                    <div class="pt-3 border-t border-slate-100">
                        <button type="submit" :disabled="rows.length === 0"
                            class="w-full py-3.5 px-4 rounded-2xl bg-brand-700 hover:bg-brand-800 disabled:opacity-50 text-white font-extrabold text-sm shadow-md transition flex items-center justify-center gap-2">
                            <i data-lucide="check" class="w-5 h-5"></i>
                            <span>Simpan Rencana Belanja</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function shoppingPlanForm() {
        return {
            allProducts: {!! json_encode($barangs) !!},
            recommendations: {!! json_encode($recommendations) !!},
            rows: [],

            init() {
                // Populate rows from recommendations
                if (this.recommendations.length > 0) {
                    this.rows = this.recommendations.map(r => ({
                        barang_id: r.barang_id,
                        jumlah_dibutuhkan: r.jumlah_rekomendasi,
                        alasan: r.alasan,
                    }));
                } else {
                    this.rows = [];
                }
            },

            addRow() {
                this.rows.push({
                    barang_id: '',
                    jumlah_dibutuhkan: 10,
                    alasan: 'Input Manual',
                });
                this.$nextTick(() => lucide.createIcons());
            },

            removeRow(index) {
                this.rows.splice(index, 1);
            }
        };
    }
</script>
@endsection
