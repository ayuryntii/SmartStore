@extends('layouts.app', ['title' => 'Kasir Penjualan', 'breadcrumb' => 'Penjualan > Kasir'])

@section('content')
<div x-data="cashierApp()" class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kasir & Transaksi Penjualan</h2>
            <p class="text-xs text-slate-500">Pilih barang, tentukan kuantitas, hitung pembayaran tunai/utang secara otomatis</p>
        </div>
        <a href="{{ route('penjualan.index') }}" class="px-3.5 py-2 rounded-xl border border-slate-300 text-slate-600 text-xs font-bold hover:bg-slate-100 transition flex items-center gap-1.5">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Daftar Transaksi</span>
        </a>
    </div>

    <form action="{{ route('penjualan.store') }}" method="POST" id="formPenjualan" @submit="submitTransaction($event)">
        @csrf
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- LEFT AREA: Item Selector & Cart Table (2 Cols) -->
            <div class="lg:col-span-2 space-y-4">
                <!-- Item Picker Card -->
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <i data-lucide="search" class="w-4 h-4"></i>
                        <span>Pilih Barang Belanjaan</span>
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                        <div class="sm:col-span-3">
                            <select id="selectBarang" x-model="selectedBarangId" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                                <option value="">-- Cari / Pilih Barang (Nama / Kode) --</option>
                                <template x-for="item in availableItems" :key="item.id">
                                    <option :value="item.id" x-text="`${item.nama_barang} [${item.kode_barang}] - Rp ${Number(item.harga_jual).toLocaleString('id-ID')} (Stok: ${Number(item.stok_saat_ini).toLocaleString('id-ID')} ${item.satuan?.singkatan || ''})`">
                                    </option>
                                </template>
                            </select>
                        </div>
                        <div>
                            <button type="button" @click="addItemToCart()"
                                class="w-full py-2.5 px-4 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md transition flex items-center justify-center gap-1.5">
                                <i data-lucide="plus" class="w-4 h-4"></i>
                                <span>Tambah</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Cart Table Card -->
                <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
                    <div class="p-4 border-b border-slate-100 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                            <i data-lucide="shopping-bag" class="w-4 h-4 text-brand-700"></i>
                            <span>Daftar Barang Belanjaan (<span x-text="cart.length"></span>)</span>
                        </h3>
                        <button type="button" @click="clearCart()" x-show="cart.length > 0" class="text-xs text-rose-600 hover:underline font-semibold">
                            Kosongkan
                        </button>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-slate-50 text-slate-400 font-semibold border-b border-slate-100">
                                    <th class="py-3 px-4">Nama Barang</th>
                                    <th class="py-3 px-4 text-right">Harga Jual</th>
                                    <th class="py-3 px-4 text-center w-28">Kuantitas</th>
                                    <th class="py-3 px-4 text-right">Subtotal</th>
                                    <th class="py-3 px-4 text-center w-12">Hapus</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 font-medium">
                                <template x-for="(item, index) in cart" :key="item.id">
                                    <tr class="hover:bg-slate-50">
                                        <td class="py-3 px-4">
                                            <input type="hidden" :name="`items[${index}][barang_id]`" :value="item.id">
                                            <span class="font-bold text-slate-900 block" x-text="item.nama_barang"></span>
                                            <span class="text-[10px] text-slate-400 font-mono" x-text="item.kode_barang"></span>
                                        </td>
                                        <td class="py-3 px-4 text-right text-slate-600">
                                            <input type="hidden" :name="`items[${index}][harga_satuan]`" :value="item.harga_jual">
                                            Rp <span x-text="Number(item.harga_jual).toLocaleString('id-ID')"></span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <input type="number" step="0.001" min="0.001" :max="item.stok_saat_ini"
                                                :name="`items[${index}][jumlah]`"
                                                x-model.number="item.jumlah"
                                                class="w-20 px-2 py-1 rounded-lg border border-slate-300 text-center font-bold text-xs focus:ring-1 focus:ring-brand-600">
                                            <span class="text-[10px] text-slate-400 block mt-0.5" x-text="item.satuan?.singkatan || 'pcs'"></span>
                                        </td>
                                        <td class="py-3 px-4 text-right font-extrabold text-slate-900">
                                            Rp <span x-text="Number(item.harga_jual * item.jumlah).toLocaleString('id-ID')"></span>
                                        </td>
                                        <td class="py-3 px-4 text-center">
                                            <button type="button" @click="removeItem(index)" class="p-1 rounded text-slate-400 hover:text-rose-600">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="cart.length === 0">
                                    <td colspan="5" class="py-12 text-center text-slate-400">
                                        <i data-lucide="shopping-cart" class="w-10 h-10 text-slate-300 mx-auto mb-2"></i>
                                        <p class="text-xs font-semibold">Keranjang belanja masih kosong.</p>
                                        <p class="text-[11px] text-slate-400">Pilih barang dari menu di atas untuk menambahkan transaksi.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT AREA: Customer, Totals & Payment (1 Col) -->
            <div class="space-y-4">
                <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-sm space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 flex items-center gap-2">
                        <i data-lucide="credit-card" class="w-4 h-4"></i>
                        <span>Informasi & Pembayaran</span>
                    </h3>

                    <!-- PILIH CARA PEMBAYARAN: TUNAI ATAU UTANG -->
                    <div class="space-y-1.5 p-3 rounded-2xl bg-slate-50 border border-slate-200">
                        <label class="block font-bold text-slate-800 text-xs">Pilih Pembayaran: Tunai atau Utang <span class="text-rose-500">*</span></label>
                        <div class="grid grid-cols-2 gap-2">
                            <button type="button" @click="setTipePembayaran('tunai')"
                                :class="tipePembayaran === 'tunai' ? 'bg-[#76944C] text-white border-[#76944C] shadow-md font-black' : 'bg-[#FFFDF8] text-slate-700 border-slate-300 hover:bg-slate-100 font-semibold'"
                                class="py-2.5 px-3 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition">
                                <i data-lucide="banknote" class="w-4 h-4"></i>
                                <span>Bayar Tunai (Lunas)</span>
                            </button>
                            <button type="button" @click="setTipePembayaran('utang')"
                                :class="tipePembayaran === 'utang' ? 'bg-[#E5BE4B] text-[#3A2D05] border-[#E5BE4B] shadow-sm font-black' : 'bg-[#FFFDF8] text-slate-700 border-slate-300 hover:bg-slate-100 font-semibold'"
                                class="py-2.5 px-3 rounded-xl border text-xs flex items-center justify-center gap-1.5 transition">
                                <i data-lucide="book-open" class="w-4 h-4"></i>
                                <span>Bayar Utang (Tempo)</span>
                            </button>
                        </div>
                    </div>

                    <!-- Nomor Faktur & Tanggal -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">No Faktur</label>
                            <input type="text" name="nomor_faktur" value="{{ $suggestedInvoice }}" required
                                class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 font-mono font-bold text-slate-800 text-xs">
                        </div>
                        <div>
                            <label class="block font-bold text-slate-600 mb-1">Tanggal</label>
                            <input type="datetime-local" name="tanggal_penjualan" value="{{ date('Y-m-d\TH:i') }}" required
                                class="w-full px-2.5 py-1.5 rounded-xl border border-slate-300 text-slate-800 text-xs">
                        </div>
                    </div>

                    <!-- Pilih Pelanggan -->
                    <div>
                        <label class="block font-bold text-slate-700 text-xs mb-1">
                            Pelanggan <span class="text-rose-500 font-bold" x-show="tipePembayaran === 'utang' || sisaTagihan > 0">*(Wajib untuk Transaksi Utang)</span>
                        </label>
                        <select name="pelanggan_id" x-model="pelangganId" :required="tipePembayaran === 'utang'"
                            class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900 focus:ring-2 focus:ring-brand-600"
                            :class="tipePembayaran === 'utang' && !pelangganId ? 'border-amber-400 ring-1 ring-amber-300' : ''">
                            <option value="">-- Pilih Pelanggan (Umum / Tetap) --</option>
                            @foreach($pelanggans as $p)
                                <option value="{{ $p->id }}">{{ $p->nama_pelanggan }} (Telp: {{ $p->nomor_telepon ?? '-' }})</option>
                            @endforeach
                        </select>
                        <p x-show="tipePembayaran === 'utang' && !pelangganId" class="text-[11px] text-amber-700 font-semibold mt-1">
                            *Pilih nama pelanggan agar utang tercatat di buku piutang.
                        </p>
                    </div>

                    <!-- Tanggal Jatuh Tempo (Muncul jika utang) -->
                    <div x-show="tipePembayaran === 'utang' || sisaTagihan > 0" x-cloak class="p-3.5 rounded-2xl bg-amber-50 border border-amber-200 space-y-2">
                        <div class="flex items-center gap-1.5 text-amber-800 text-xs font-bold">
                            <i data-lucide="alert-circle" class="w-4 h-4 text-amber-600"></i>
                            <span>Ketentuan Transaksi Utang / Tempo</span>
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-amber-900 mb-1">Batas Tanggal Jatuh Tempo Pembayaran</label>
                            <input type="date" name="tanggal_jatuh_tempo" value="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                class="w-full px-2.5 py-1.5 rounded-xl border border-amber-300 text-xs text-slate-900 bg-white">
                        </div>
                    </div>

                    <!-- Summary Ringkasan Total -->
                    <div class="pt-3 border-t border-slate-100 space-y-2 text-xs">
                        <div class="flex justify-between text-slate-500">
                            <span>Subtotal Barang:</span>
                            <span class="font-bold text-slate-800">Rp <span x-text="Number(subtotal).toLocaleString('id-ID')"></span></span>
                        </div>
                        <div class="flex items-center justify-between text-slate-500">
                            <span>Potongan / Diskon (Rp):</span>
                            <input type="number" min="0" step="100" name="diskon" x-model.number="diskon"
                                class="w-28 px-2 py-1 rounded-lg border border-slate-300 text-right font-bold text-xs">
                        </div>
                        <div class="flex justify-between items-center pt-2 border-t border-slate-100">
                            <span class="font-extrabold text-sm text-slate-900">Total Tagihan:</span>
                            <span class="font-black text-xl text-brand-700">Rp <span x-text="Number(grandTotal).toLocaleString('id-ID')"></span></span>
                        </div>
                    </div>

                    <!-- Bagian Pembayaran -->
                    <div class="pt-3 border-t border-slate-100 space-y-3">
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1">Metode Pembayaran</label>
                            <select name="metode_pembayaran" x-model="metodePembayaran" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900">
                                <option value="tunai">Tunai / Uang Pas</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="qris">QRIS</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1" x-text="tipePembayaran === 'tunai' ? 'Jumlah Uang Tunai Diterima (Rp)' : 'Uang Muka / DP Sekarang (Rp, opsional)'"></label>
                            <input type="number" min="0" step="100" name="jumlah_bayar" x-model.number="jumlahBayar"
                                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-base font-black text-slate-900 focus:ring-2 focus:ring-brand-600">
                        </div>

                        <!-- Quick Cash Shortcuts -->
                        <template x-if="tipePembayaran === 'tunai'">
                            <div class="grid grid-cols-4 gap-1 text-[10px]">
                                <button type="button" @click="jumlahBayar = grandTotal" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">Uang Pas</button>
                                <button type="button" @click="jumlahBayar = 20000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">20.000</button>
                                <button type="button" @click="jumlahBayar = 50000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">50.000</button>
                                <button type="button" @click="jumlahBayar = 100000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">100.000</button>
                            </div>
                        </template>
                        <template x-if="tipePembayaran === 'utang'">
                            <div class="grid grid-cols-4 gap-1 text-[10px]">
                                <button type="button" @click="jumlahBayar = 0" class="py-1 rounded bg-amber-100 hover:bg-amber-200 font-bold text-amber-800">DP 0 (Penuh)</button>
                                <button type="button" @click="jumlahBayar = 10000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">DP 10.000</button>
                                <button type="button" @click="jumlahBayar = 20000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">DP 20.000</button>
                                <button type="button" @click="jumlahBayar = 50000" class="py-1 rounded bg-slate-100 hover:bg-slate-200 font-bold text-slate-700">DP 50.000</button>
                            </div>
                        </template>

                        <!-- Kembalian / Sisa Tagihan (Utang) -->
                        <div class="p-3.5 rounded-2xl border" :class="sisaTagihan === 0 ? 'bg-emerald-50 border-emerald-200' : 'bg-amber-50 border-amber-300'">
                            <template x-if="sisaTagihan === 0">
                                <div class="flex justify-between items-center text-emerald-800">
                                    <span class="font-bold text-xs">Kembalian Konsumen:</span>
                                    <span class="font-black text-base">Rp <span x-text="Number(kembalian).toLocaleString('id-ID')"></span></span>
                                </div>
                            </template>
                            <template x-if="sisaTagihan > 0">
                                <div class="flex justify-between items-center text-amber-900">
                                    <div>
                                        <span class="font-bold text-xs block">Sisa Utang Dicatat:</span>
                                        <span class="text-[10px] text-amber-700 font-normal">Masuk ke Buku Piutang Konsumen</span>
                                    </div>
                                    <span class="font-black text-base text-rose-600">Rp <span x-text="Number(sisaTagihan).toLocaleString('id-ID')"></span></span>
                                </div>
                            </template>
                        </div>

                        <!-- Status Transaksi -->
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1">Status Transaksi</label>
                            <select name="status_penjualan" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs text-slate-900">
                                <option value="selesai" selected>Selesai (Langsung Kurangi Stok)</option>
                                <option value="draf">Draf (Simpan Draf Saja)</option>
                            </select>
                        </div>

                        <!-- Catatan -->
                        <div>
                            <label class="block font-bold text-slate-700 text-xs mb-1">Catatan Tambahan</label>
                            <textarea name="catatan" rows="2" placeholder="Catatan transaksi..."
                                class="w-full px-3 py-1.5 rounded-xl border border-slate-300 text-xs text-slate-900"></textarea>
                        </div>

                        <!-- Submit Button (Slide 20: Proses Transaksi) -->
                        <button type="submit" :disabled="cart.length === 0"
                            class="w-full py-3.5 px-4 rounded-2xl bg-[#76944C] hover:bg-[#688442] disabled:opacity-50 text-white font-black text-sm shadow-md transition flex items-center justify-center gap-2">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            <span>Proses Transaksi</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
    function cashierApp() {
        return {
            availableItems: {!! json_encode($barangs) !!},
            cart: [],
            selectedBarangId: '',
            pelangganId: '',
            diskon: 0,
            jumlahBayar: 0,
            metodePembayaran: 'tunai',
            tipePembayaran: '{{ request("tipe", "tunai") }}',

            init() {
                if (this.tipePembayaran === 'tunai') {
                    this.$watch('grandTotal', val => {
                        if (this.tipePembayaran === 'tunai') {
                            this.jumlahBayar = val;
                        }
                    });
                }
            },

            setTipePembayaran(tipe) {
                this.tipePembayaran = tipe;
                if (tipe === 'tunai') {
                    this.jumlahBayar = this.grandTotal;
                } else {
                    if (this.jumlahBayar >= this.grandTotal) {
                        this.jumlahBayar = 0;
                    }
                }
            },

            submitTransaction(event) {
                if (this.tipePembayaran === 'utang' && !this.pelangganId) {
                    event.preventDefault();
                    alert('PERHATIAN: Untuk transaksi utang/tempo, Pelanggan wajib dipilih terlebih dahulu agar tercatat di buku utang!');
                    return false;
                }
            },

            get subtotal() {
                return this.cart.reduce((sum, item) => sum + (item.harga_jual * item.jumlah), 0);
            },
            get grandTotal() {
                return Math.max(0, this.subtotal - (this.diskon || 0));
            },
            get kembalian() {
                return Math.max(0, (this.jumlahBayar || 0) - this.grandTotal);
            },
            get sisaTagihan() {
                return Math.max(0, this.grandTotal - (this.jumlahBayar || 0));
            },

            addItemToCart() {
                if (!this.selectedBarangId) return;
                const found = this.availableItems.find(b => b.id == this.selectedBarangId);
                if (!found) return;

                const existing = this.cart.find(c => c.id == found.id);
                if (existing) {
                    existing.jumlah += 1;
                } else {
                    this.cart.push({
                        id: found.id,
                        nama_barang: found.nama_barang,
                        kode_barang: found.kode_barang,
                        harga_jual: Number(found.harga_jual),
                        stok_saat_ini: Number(found.stok_saat_ini),
                        satuan: found.satuan,
                        jumlah: 1,
                    });
                }
                this.selectedBarangId = '';
                this.$nextTick(() => lucide.createIcons());
            },

            removeItem(index) {
                this.cart.splice(index, 1);
            },

            clearCart() {
                this.cart = [];
            }
        };
    }
</script>
@endsection
