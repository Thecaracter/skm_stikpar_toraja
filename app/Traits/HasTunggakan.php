<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;

trait HasTunggakan
{
    public static function bootHasTunggakan()
    {
        // Saat membuat tagihan baru
        static::created(function ($model) {
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
        static::updated(function ($model) {
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
        static::deleted(function ($model) {
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
     * Mendapatkan total tunggakan per jenis pembayaran
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
     * Mendapatkan detail tunggakan semester tertentu
     */
    public function getTunggakanBySemester($semester, $tahunAjaran)
    {
        return $this->where('user_id', $this->user_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', '!=', 'lunas')
            ->with('jenis_pembayaran')
            ->get();
    }

    /**
     * Mendapatkan ringkasan tunggakan
     */
    public function getTunggakanSummary()
    {
        $tunggakan = $this->where('user_id', $this->user_id)
            ->where('status', '!=', 'lunas')
            ->get();

        return [
            'total_tunggakan' => $tunggakan->sum('sisa_tagihan'),
            'jumlah_tagihan' => $tunggakan->count(),
            'tagihan_belum_bayar' => $tunggakan->where('status', 'belum_bayar')->count(),
            'tagihan_cicilan' => $tunggakan->where('status', 'cicilan')->count(),
            'semester_tertunggak' => $tunggakan->pluck('semester')->unique()->count()
        ];
    }

    /**
     * Cek apakah mahasiswa memiliki tunggakan
     */
    public function hasTunggakan(): bool
    {
        return $this->user->total_tunggakan > 0;
    }

    /**
     * Cek apakah memiliki tunggakan untuk semester tertentu
     */
    public function hasTunggakanSemester($semester, $tahunAjaran): bool
    {
        return $this->where('user_id', $this->user_id)
            ->where('semester', $semester)
            ->where('tahun_ajaran', $tahunAjaran)
            ->where('status', '!=', 'lunas')
            ->exists();
    }

    /**
     * Mendapatkan history pembayaran tunggakan
     */
    public function getTunggakanPaymentHistory()
    {
        return $this->where('user_id', $this->user_id)
            ->with([
                'pembayaran' => function ($query) {
                    $query->orderBy('created_at', 'desc');
                },
                'jenis_pembayaran'
            ])
            ->get()
            ->map(function ($tagihan) {
                return [
                    'id' => $tagihan->id,
                    'jenis_pembayaran' => $tagihan->jenis_pembayaran->nama,
                    'semester' => $tagihan->semester,
                    'tahun_ajaran' => $tagihan->tahun_ajaran,
                    'jumlah_tagihan' => $tagihan->jumlah_tagihan,
                    'sisa_tagihan' => $tagihan->sisa_tagihan,
                    'status' => $tagihan->status,
                    'pembayaran' => $tagihan->pembayaran->map(function ($pembayaran) {
                        return [
                            'tanggal' => $pembayaran->created_at,
                            'jumlah' => $pembayaran->jumlah_bayar,
                            'status' => $pembayaran->status,
                            'verifikasi_oleh' => $pembayaran->verifikator->name ?? null,
                            'tanggal_verifikasi' => $pembayaran->tanggal_verifikasi
                        ];
                    })
                ];
            });
    }

    /**
     * Cek status kelayakan untuk pendaftaran semester
     */
    public function checkRegistrationEligibility(): array
    {
        $tunggakan = $this->getTunggakanSummary();
        $eligible = $tunggakan['total_tunggakan'] <= 0;

        return [
            'eligible' => $eligible,
            'tunggakan' => $tunggakan,
            'message' => $eligible
                ? 'Mahasiswa dapat mendaftar semester baru'
                : 'Mahasiswa memiliki tunggakan yang harus diselesaikan sebelum pendaftaran'
        ];
    }
}