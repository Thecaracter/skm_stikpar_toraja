<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'jumlah_bayar',
        'bukti_pembayaran',
        'status',
        'catatan',
        'tanggal_verifikasi',
        'verifikasi_oleh',
        'deleted_at'
    ];

    protected $casts = [
        'jumlah_bayar' => 'decimal:2',
        'tanggal_verifikasi' => 'datetime',
        'deleted_at' => 'datetime',
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