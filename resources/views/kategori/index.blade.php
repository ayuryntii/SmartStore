@extends('layouts.app', ['title' => 'Kategori Barang', 'breadcrumb' => 'Data Barang > Kategori'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Kategori Produk</h2>
            <p class="text-xs text-slate-500">Pengelompokan barang warung (Makanan, Minuman, Sembako, dll.)</p>
        </div>
        <button type="button" onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Nama Kategori</th>
                        <th class="py-3 px-4">Deskripsi</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang Terkait</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($kategori as $kat)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-slate-400">#{{ $kat->id }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $kat->nama_kategori }}</td>
                            <td class="py-3 px-4 text-slate-500">{{ $kat->deskripsi ?? '-' }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $kat->barang_count }} Produk
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button"
                                        onclick="openEditModal('{{ $kat->id }}', '{{ addslashes($kat->nama_kategori) }}', '{{ addslashes($kat->deskripsi ?? '') }}')"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-brand-700 hover:bg-slate-100">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    @if($kat->barang_count == 0)
                                        <form action="{{ route('kategori.destroy', $kat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-lg text-slate-500 hover:text-rose-600 hover:bg-rose-50">
                                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada kategori terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Kategori -->
<div id="modalKategori" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 id="modalTitle" class="text-base font-bold text-slate-900 mb-4">Tambah Kategori Baru</h3>
        <form id="formKategori" method="POST" action="{{ route('kategori.store') }}">
            @csrf
            <div id="methodField"></div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Kategori <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_kategori" name="nama_kategori" required
                        placeholder="Contoh: Bumbu Dapur"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Deskripsi</label>
                    <textarea id="deskripsi" name="deskripsi" rows="3"
                        placeholder="Keterangan kategori..."
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600"></textarea>
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-md">
                    Simpan Kategori
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Kategori Baru';
        document.getElementById('formKategori').action = "{{ route('kategori.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('nama_kategori').value = '';
        document.getElementById('deskripsi').value = '';
        document.getElementById('modalKategori').classList.remove('hidden');
    }

    function openEditModal(id, nama, desc) {
        document.getElementById('modalTitle').textContent = 'Ubah Kategori';
        document.getElementById('formKategori').action = "{{ url('/kategori') }}/" + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('nama_kategori').value = nama;
        document.getElementById('deskripsi').value = desc;
        document.getElementById('modalKategori').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalKategori').classList.add('hidden');
    }
</script>
@endsection
