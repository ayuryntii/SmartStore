<?php

namespace App\Services;

use App\Models\Pelanggan;
use App\Models\PembayaranPenjualan;
use App\Models\Penjualan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class DebtPaymentService
{
    /**
     * Record payment for a specific sales invoice
     */
    public function payInvoice(
        int $penjualanId,
        float $jumlahBayar,
        string $metodePembayaran = 'tunai',
        ?string $tanggalPembayaran = null,
        ?string $catatan = null,
        ?int $penggunaId = null
    ): PembayaranPenjualan {
        if ($jumlahBayar <= 0) {
            throw new InvalidArgumentException('Jumlah pembayaran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use (
            $penjualanId,
            $jumlahBayar,
            $metodePembayaran,
            $tanggalPembayaran,
            $catatan,
            $penggunaId
        ) {
            $userId = $penggunaId ?? Auth::id();
            $penjualan = Penjualan::where('id', $penjualanId)->lockForUpdate()->firstOrFail();

            if ($penjualan->status_penjualan === 'dibatalkan') {
                throw new InvalidArgumentException('Tidak dapat melakukan pembayaran untuk transaksi yang dibatalkan.');
            }

            $sisaTagihan = $penjualan->sisa_tagihan;

            if ($sisaTagihan <= 0.001) {
                throw new InvalidArgumentException("Transaksi faktur {$penjualan->nomor_faktur} sudah lunas.");
            }

            if ($jumlahBayar > $sisaTagihan + 0.001) {
                throw new InvalidArgumentException("Jumlah bayar (Rp " . number_format($jumlahBayar, 0, ',', '.') . ") melebihi sisa utang (Rp " . number_format($sisaTagihan, 0, ',', '.') . ").");
            }

            $tgl = $tanggalPembayaran ?? now();

            $pembayaran = PembayaranPenjualan::create([
                'penjualan_id' => $penjualan->id,
                'pengguna_id' => $userId,
                'jumlah_bayar' => $jumlahBayar,
                'metode_pembayaran' => $metodePembayaran,
                'tanggal_pembayaran' => $tgl,
                'catatan' => $catatan,
            ]);

            // Recalculate accumulated payments to guarantee consistency
            $totalBayarAkumulasi = PembayaranPenjualan::where('penjualan_id', $penjualan->id)->sum('jumlah_bayar');
            $penjualan->total_bayar = $totalBayarAkumulasi;
            $penjualan->save();

            $pelangganNama = $penjualan->pelanggan?->nama_pelanggan ?? 'Umum';
            ActivityLogService::log(
                'bayar_piutang',
                'pembayaran_penjualan',
                $pembayaran->id,
                "Mencatat pembayaran utang pelanggan {$pelangganNama} faktur {$penjualan->nomor_faktur} sebesar Rp " . number_format($jumlahBayar, 0, ',', '.'),
                $userId
            );

            return $pembayaran;
        });
    }

    /**
     * Settle customer debt across unpaid invoices (oldest first)
     */
    public function payCustomerDebt(
        int $pelangganId,
        float $jumlahBayar,
        string $metodePembayaran = 'tunai',
        ?string $catatan = null,
        ?int $penggunaId = null
    ): array {
        if ($jumlahBayar <= 0) {
            throw new InvalidArgumentException('Jumlah pembayaran harus lebih besar dari 0.');
        }

        return DB::transaction(function () use (
            $pelangganId,
            $jumlahBayar,
            $metodePembayaran,
            $catatan,
            $penggunaId
        ) {
            $userId = $penggunaId ?? Auth::id();
            $pelanggan = Pelanggan::findOrFail($pelangganId);

            $unpaidSales = Penjualan::where('pelanggan_id', $pelangganId)
                ->where('status_penjualan', 'selesai')
                ->whereRaw('(total_harga - diskon) > total_bayar')
                ->orderBy('tanggal_penjualan', 'asc')
                ->lockForUpdate()
                ->get();

            $totalPiutang = $unpaidSales->sum(fn($s) => $s->sisa_tagihan);
            if ($totalPiutang <= 0.001) {
                throw new InvalidArgumentException("Pelanggan {$pelanggan->nama_pelanggan} tidak memiliki utang yang belum dibayar.");
            }

            if ($jumlahBayar > $totalPiutang + 0.001) {
                throw new InvalidArgumentException("Jumlah bayar melebihi total utang pelanggan (Total Utang: Rp " . number_format($totalPiutang, 0, ',', '.') . ").");
            }

            $sisaBayar = $jumlahBayar;
            $recordedPayments = [];

            foreach ($unpaidSales as $sale) {
                if ($sisaBayar <= 0) {
                    break;
                }

                $sisaTagihanSale = $sale->sisa_tagihan;
                $alokasi = min($sisaBayar, $sisaTagihanSale);

                $pembayaran = PembayaranPenjualan::create([
                    'penjualan_id' => $sale->id,
                    'pengguna_id' => $userId,
                    'jumlah_bayar' => $alokasi,
                    'metode_pembayaran' => $metodePembayaran,
                    'tanggal_pembayaran' => now(),
                    'catatan' => $catatan ?? "Pelunasan utang pelanggan {$pelanggan->nama_pelanggan}",
                ]);

                $totalBayarAkumulasi = PembayaranPenjualan::where('penjualan_id', $sale->id)->sum('jumlah_bayar');
                $sale->total_bayar = $totalBayarAkumulasi;
                $sale->save();

                $sisaBayar -= $alokasi;
                $recordedPayments[] = $pembayaran;
            }

            ActivityLogService::log(
                'pelunasan_utang_pelanggan',
                'pelanggan',
                $pelanggan->id,
                "Pembayaran utang pelanggan {$pelanggan->nama_pelanggan} total Rp " . number_format($jumlahBayar, 0, ',', '.'),
                $userId
            );

            return [
                'success' => true,
                'total_dibayar' => $jumlahBayar,
                'payments' => $recordedPayments,
            ];
        });
    }
}
