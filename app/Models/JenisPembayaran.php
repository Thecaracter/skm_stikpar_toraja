<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    use HasFactory;

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama',
        'keterangan',
        'nominal',
        'dapat_dicicil',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'dapat_dicicil' => 'boolean',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }
}
