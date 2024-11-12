<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait AutoCalculateSisaTagihan
{
    public static function bootAutoCalculateSisaTagihan()
    {
        // Sebelum menyimpan data
        static::saving(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    // Hitung sisa tagihan
                    $model->sisa_tagihan = $model->jumlah_tagihan - $model->jumlah_terbayar;

                    // Update status berdasarkan kondisi pembayaran
                    $model->status = match (true) {
                        $model->sisa_tagihan <= 0 => 'lunas',
                        $model->jumlah_terbayar > 0 => 'cicilan',
                        default => 'belum_bayar'
                    };

                    // Atur cicilan jika dapat dicicil
                    if ($model->jenis_pembayaran && $model->jenis_pembayaran->dapat_dicicil) {
                        $model->updateCicilanInfo();
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Error calculating sisa tagihan: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Update informasi cicilan
     */
    protected function updateCicilanInfo(): void
    {
        if ($this->jumlah_terbayar > 0) {
            $totalCicilan = $this->total_cicilan ?? 6; // Default 6 cicilan jika tidak diset
            $nominalPerCicilan = $this->jumlah_tagihan / $totalCicilan;
            $this->cicilan_ke = ceil($this->jumlah_terbayar / $nominalPerCicilan);
        }
    }

    /**
     * Cek status tagihan
     */
    public function isLunas(): bool
    {
        return $this->status === 'lunas';
    }

    public function isCicilan(): bool
    {
        return $this->status === 'cicilan';
    }

    public function isBelumBayar(): bool
    {
        return $this->status === 'belum_bayar';
    }

    /**
     * Mendapatkan persentase pembayaran
     */
    public function getPersentasePembayaran(): float
    {
        if ($this->jumlah_tagihan <= 0) {
            return 0;
        }

        return round(($this->jumlah_terbayar / $this->jumlah_tagihan) * 100, 2);
    }

    /**
     * Mendapatkan informasi cicilan
     */
    public function getCicilanInfo(): array
    {
        $totalCicilan = $this->total_cicilan ?? 6;
        $nominalPerCicilan = $this->jumlah_tagihan / $totalCicilan;

        return [
            'total_cicilan' => $totalCicilan,
            'cicilan_ke' => $this->cicilan_ke ?? 0,
            'nominal_per_cicilan' => $nominalPerCicilan,
            'sisa_cicilan' => $totalCicilan - ($this->cicilan_ke ?? 0),
            'total_terbayar' => $this->jumlah_terbayar,
            'sisa_tagihan' => $this->sisa_tagihan
        ];
    }

    /**
     * Validasi pembayaran cicilan
     */
    public function validatePembayaranCicilan($jumlahBayar): bool
    {
        if (!$this->jenis_pembayaran->dapat_dicicil) {
            throw new \Exception('Pembayaran ini tidak dapat dicicil');
        }

        $cicilanInfo = $this->getCicilanInfo();
        $toleransi = 1000; // toleransi selisih pembayaran (dapat disesuaikan)

        return abs($jumlahBayar - $cicilanInfo['nominal_per_cicilan']) <= $toleransi;
    }

    /**
     * Mendapatkan history pembayaran
     */
    public function getPembayaranHistory()
    {
        return $this->pembayaran()
            ->orderBy('created_at', 'desc')
            ->with('verifikator')
            ->get()
            ->map(function ($pembayaran) {
                return [
                    'tanggal' => $pembayaran->created_at,
                    'jumlah_bayar' => $pembayaran->jumlah_bayar,
                    'status' => $pembayaran->status,
                    'verifikator' => $pembayaran->verifikator ? $pembayaran->verifikator->name : null,
                    'tanggal_verifikasi' => $pembayaran->tanggal_verifikasi
                ];
            });
    }
}