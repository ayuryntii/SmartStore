<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\RencanaBelanja;
use App\Services\ShoppingPlanService;
use Illuminate\Http\Request;

class RencanaBelanjaController extends Controller
{
    public function __construct(
        protected ShoppingPlanService $shoppingPlanService
    ) {}

    public function index(Request $request)
    {
        $status = $request->input('status_rencana');
        $query = RencanaBelanja::with(['pengguna', 'detailRencanaBelanja.barang.satuan'])
            ->orderBy('tanggal_rencana', 'desc')
            ->orderBy('id', 'desc');

        if ($status) {
            $query->where('status_rencana', $status);
        }

        $rencana = $query->paginate(15)->withQueryString();

        return view('rencana_belanja.index', compact('rencana', 'status'));
    }

    public function create()
    {
        $recommendations = $this->shoppingPlanService->generateRecommendations();
        $barangs = Barang::where('status_aktif', 1)->with('satuan')->orderBy('nama_barang')->get();

        return view('rencana_belanja.create', compact('recommendations', 'barangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tanggal_rencana' => 'required|date',
            'status_rencana' => 'required|in:draf,dipesan,selesai,dibatalkan',
            'catatan' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.barang_id' => 'required|exists:barang,id',
            'items.*.jumlah_dibutuhkan' => 'required|numeric|min:0.001',
        ], [
            'items.required' => 'Masukkan minimal satu barang untuk rencana belanja.',
            'items.*.jumlah_dibutuhkan.min' => 'Jumlah yang dibutuhkan harus lebih dari 0.',
        ]);

        try {
            $plan = $this->shoppingPlanService->createPlan($validated);

            return redirect()->route('rencana-belanja.show', $plan->id)
                ->with('success', 'Rencana belanja berhasil dibuat!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(RencanaBelanja $rencanaBelanja)
    {
        $rencanaBelanja->load(['pengguna', 'detailRencanaBelanja.barang.satuan', 'detailRencanaBelanja.barang.kategori']);

        return view('rencana_belanja.show', compact('rencanaBelanja'));
    }

    public function updateItem(Request $request, int $detailId)
    {
        $validated = $request->validate([
            'status_item' => 'required|in:menunggu,dibeli,dibatalkan',
            'jumlah_dibeli' => 'nullable|numeric|min:0',
        ]);

        try {
            $this->shoppingPlanService->updateItemStatus(
                $detailId,
                $validated['status_item'],
                (float) ($validated['jumlah_dibeli'] ?? 0)
            );

            return back()->with('success', 'Status barang belanja berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateStatus(Request $request, RencanaBelanja $rencanaBelanja)
    {
        $validated = $request->validate([
            'status_rencana' => 'required|in:draf,dipesan,selesai,dibatalkan',
        ]);

        $rencanaBelanja->update(['status_rencana' => $validated['status_rencana']]);

        return back()->with('success', 'Status rencana belanja berhasil diubah.');
    }
}
