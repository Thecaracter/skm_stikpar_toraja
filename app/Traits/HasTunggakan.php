<?php
namespace App\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

trait HasTunggakan
{
    public static function bootHasTunggakan()
    {
        // Saat membuat tagihan baru
        static::created(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    $model->user->increment('total_tunggakan', $model->jumlah_tagihan);
                });
            } catch (\Exception $e) {
                \Log::error('Error updating tunggakan on create: ' . $e->getMessage());
                throw $e;
            }
        });

        // Saat mengupdate tagihan
        static::updated(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    // Update tunggakan jika ada perubahan jumlah tagihan
                    if ($model->wasChanged('jumlah_tagihan')) {
                        $selisih = $model->jumlah_tagihan - $model->getOriginal('jumlah_tagihan');
                        if ($selisih > 0) {
                            $model->user->increment('total_tunggakan', $selisih);
                        } else {
                            $model->user->decrement('total_tunggakan', abs($selisih));
                        }
                    }

                    // Update tunggakan jika ada perubahan jumlah terbayar
                    if ($model->wasChanged('jumlah_terbayar')) {
                        $selisihPembayaran = $model->jumlah_terbayar - $model->getOriginal('jumlah_terbayar');
                        if ($selisihPembayaran > 0) {
                            $model->user->decrement('total_tunggakan', $selisihPembayaran);
                        } else {
                            $model->user->increment('total_tunggakan', abs($selisihPembayaran));
                        }
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Error updating tunggakan on update: ' . $e->getMessage());
                throw $e;
            }
        });

        // Saat menghapus tagihan
        static::deleted(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    if ($model->sisa_tagihan > 0) {
                        $model->user->decrement('total_tunggakan', $model->sisa_tagihan);
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Error updating tunggakan on delete: ' . $e->getMessage());
                throw $e;
            }
        });

        // Saat mengembalikan tagihan yang dihapus (restore)
        static::restored(function (Model $model) {
            try {
                DB::transaction(function () use ($model) {
                    if ($model->sisa_tagihan > 0) {
                        $model->user->increment('total_tunggakan', $model->sisa_tagihan);
                    }
                });
            } catch (\Exception $e) {
                \Log::error('Error updating tunggakan on restore: ' . $e->getMessage());
                throw $e;
            }
        });
    }

    /**
     * Hitung ulang total tunggakan untuk user tertentu
     */
    public function recalculateTotalTunggakan(): float
    {
        try {
            return DB::transaction(function () {
                $totalTunggakan = $this->where('user_id', $this->user_id)
                    ->where('status', '!=', 'lunas')
                    ->sum('sisa_tagihan');

                $this->user->update(['total_tunggakan' => $totalTunggakan]);

                return $totalTunggakan;
            });
        } catch (\Exception $e) {
            \Log::error('Error recalculating tunggakan: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Mendapatkan semua tunggakan yang masih aktif
     */
    public function getActiveTunggakan()
    {
        return $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Mendapatkan total tunggakan berdasarkan jenis pembayaran
     */
    public function getTunggakanByJenis()
    {
        return $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->select('jenis_pembayaran_id', DB::raw('SUM(sisa_tagihan) as total'))
            ->groupBy('jenis_pembayaran_id')
            ->with('jenis_pembayaran')
            ->get();
    }

    /**
     * Cek apakah mahasiswa memiliki tunggakan
     */
    public function hasTunggakan(): bool
    {
        return $this->user->total_tunggakan > 0;
    }
}