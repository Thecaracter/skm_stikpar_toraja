<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Traits\HasTunggakan;
use App\Traits\AutoCalculateSisaTagihan;

class Tagihan extends Model
{
    use HasFactory, HasTunggakan, AutoCalculateSisaTagihan, SoftDeletes;

    protected $table = 'tagihan';

    protected $fillable = [
        'user_id',
        'jenis_pembayaran_id',
        'semester',
        'tahun_ajaran',
        'jumlah_tagihan',
        'jumlah_terbayar',
        'sisa_tagihan',
        'cicilan_ke',
        'total_cicilan',
        'status',
    ];

    protected $casts = [
        'jumlah_tagihan' => 'decimal:2',
        'jumlah_terbayar' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
        'cicilan_ke' => 'integer',
        'total_cicilan' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function jenis_pembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }
}