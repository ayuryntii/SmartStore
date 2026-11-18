@extends('layouts.app', ['title' => 'Rencana Belanja', 'breadcrumb' => 'Pengadaan > Rencana Belanja'])

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Perencanaan Belanja & Kulakan</h2>
            <p class="text-xs text-slate-500">Rekomendasi otomatis berbasis stok habis & menipis untuk efisiensi modal belanja warung</p>
        </div>
        <a href="{{ route('rencana-belanja.create') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-brand-700 hover:bg-brand-800 text-white text-xs font-bold shadow-md shadow-brand-700/20 transition">
            <i data-lucide="plus-circle" class="w-4 h-4"></i>
            <span>Buat Rencana Belanja</span>
        </a>
    </div>

    <!-- Filter by Status -->
    <div class="flex items-center gap-2 overflow-x-auto pb-1 text-xs font-bold">
        <a href="{{ route('rencana-belanja.index') }}"
            class="px-3.5 py-1.5 rounded-xl border {{ empty($status) ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Semua
        </a>
        <a href="{{ route('rencana-belanja.index', ['status_rencana' => 'draf']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $status === 'draf' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Draf
        </a>
        <a href="{{ route('rencana-belanja.index', ['status_rencana' => 'dipesan']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $status === 'dipesan' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Dipesan
        </a>
        <a href="{{ route('rencana-belanja.index', ['status_rencana' => 'selesai']) }}"
            class="px-3.5 py-1.5 rounded-xl border {{ $status === 'selesai' ? 'bg-slate-900 text-white border-slate-900' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' }}">
            Selesai
        </a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="bg-slate-50 text-slate-500 font-semibold border-b border-slate-200">
                        <th class="py-3 px-4">ID Rencana</th>
                        <th class="py-3 px-4">Tanggal Rencana</th>
                        <th class="py-3 px-4">Dibuat Oleh</th>
                        <th class="py-3 px-4">Catatan</th>
                        <th class="py-3 px-4 text-center">Total Item</th>
                        <th class="py-3 px-4 text-center">Status</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-medium">
                    @forelse($rencana as $item)
                        <tr class="hover:bg-slate-50">
                            <td class="py-3 px-4 font-mono font-bold text-slate-800">
                                <a href="{{ route('rencana-belanja.show', $item->id) }}" class="hover:text-brand-700">
                                    #RB-{{ str_pad($item->id, 4, '0', STR_PAD_LEFT) }}
                                </a>
                            </td>
                            <td class="py-3 px-4 text-slate-600 font-mono text-[11px]">
                                {{ $item->tanggal_rencana->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 text-slate-700 font-bold">
                                {{ $item->pengguna?->nama ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-slate-500">
                                {{ $item->catatan ?? '-' }}
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-slate-100 text-slate-700">
                                    {{ $item->detailRencanaBelanja->count() }} Barang
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($item->status_rencana === 'selesai')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-emerald-100 text-emerald-800">Selesai</span>
                                @elseif($item->status_rencana === 'dipesan')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-blue-100 text-blue-800">Dipesan</span>
                                @elseif($item->status_rencana === 'draf')
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-amber-100 text-amber-800">Draf</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold bg-slate-200 text-slate-700">Dibatalkan</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('rencana-belanja.show', $item->id) }}" class="px-3 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-700 hover:bg-slate-200">
                                    Kelola &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-400">Belum ada rencana belanja terdaftar.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($rencana->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $rencana->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
