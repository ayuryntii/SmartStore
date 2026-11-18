@extends('layouts.app', ['title' => 'Satuan Barang', 'breadcrumb' => 'Data Barang > Satuan'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Satuan Barang</h2>
            <p class="text-xs text-slate-500">Daftar satuan unit barang warung (Pcs, Bungkus, Dus, Kilogram, dll.)</p>
        </div>
        <button type="button" onclick="openAddModal()"
            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Tambah Satuan</span>
        </button>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">ID</th>
                        <th class="py-3 px-4">Nama Satuan</th>
                        <th class="py-3 px-4">Singkatan</th>
                        <th class="py-3 px-4 text-center">Jumlah Barang Terkait</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($satuan as $sat)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono text-slate-400">#{{ $sat->id }}</td>
                            <td class="py-3 px-4 font-bold text-slate-900">{{ $sat->nama_satuan }}</td>
                            <td class="py-3 px-4 font-mono text-slate-600 font-bold bg-slate-50/50 inline-block my-2 px-2 py-0.5 rounded">
                                {{ $sat->singkatan }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $sat->barang_count }} Produk
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <div class="inline-flex items-center gap-2">
                                    <button type="button"
                                        onclick="openEditModal('{{ $sat->id }}', '{{ addslashes($sat->nama_satuan) }}', '{{ addslashes($sat->singkatan) }}')"
                                        class="p-1.5 rounded-lg text-slate-500 hover:text-brand-700 hover:bg-slate-100">
                                        <i data-lucide="edit" class="w-4 h-4"></i>
                                    </button>
                                    @if($sat->barang_count == 0)
                                        <form action="{{ route('satuan.destroy', $sat->id) }}" method="POST" onsubmit="return confirm('Hapus satuan ini?')">
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
                            <td colspan="5" class="py-8 text-center text-slate-400">Belum ada data satuan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Form Satuan -->
<div id="modalSatuan" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm hidden">
    <div class="bg-white rounded-3xl max-w-md w-full p-6 shadow-2xl border border-slate-100">
        <h3 id="modalTitle" class="text-base font-bold text-slate-900 mb-4">Tambah Satuan Baru</h3>
        <form id="formSatuan" method="POST" action="{{ route('satuan.store') }}">
            @csrf
            <div id="methodField"></div>
            <div class="space-y-4">
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Nama Satuan <span class="text-rose-500">*</span></label>
                    <input type="text" id="nama_satuan" name="nama_satuan" required
                        placeholder="Contoh: Kilogram"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 focus:outline-none focus:ring-2 focus:ring-brand-600">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-700 mb-1">Singkatan <span class="text-rose-500">*</span></label>
                    <input type="text" id="singkatan" name="singkatan" required
                        placeholder="Contoh: kg"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs text-slate-900 font-mono focus:outline-none focus:ring-2 focus:ring-brand-600">
                </div>
            </div>
            <div class="flex items-center justify-end gap-2 mt-6">
                <button type="button" onclick="closeModal()" class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:bg-slate-100">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 rounded-xl text-xs font-bold text-white bg-brand-700 hover:bg-brand-800 shadow-md">
                    Simpan Satuan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAddModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Satuan Baru';
        document.getElementById('formSatuan').action = "{{ route('satuan.store') }}";
        document.getElementById('methodField').innerHTML = '';
        document.getElementById('nama_satuan').value = '';
        document.getElementById('singkatan').value = '';
        document.getElementById('modalSatuan').classList.remove('hidden');
    }

    function openEditModal(id, nama, singkatan) {
        document.getElementById('modalTitle').textContent = 'Ubah Satuan';
        document.getElementById('formSatuan').action = "{{ url('/satuan') }}/" + id;
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        document.getElementById('nama_satuan').value = nama;
        document.getElementById('singkatan').value = singkatan;
        document.getElementById('modalSatuan').classList.remove('hidden');
    }

    function closeModal() {
        document.getElementById('modalSatuan').classList.add('hidden');
    }
</script>
@endsection
