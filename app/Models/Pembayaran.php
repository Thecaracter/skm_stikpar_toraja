<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    use HasFactory;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'jumlah_bayar',
        'bukti_pembayaran',
        'status',
        'catatan',
        'tanggal_verifikasi',
        'verifikasi_oleh',
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_verifikasi' => 'datetime',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'verifikasi_oleh');
    }
}
