<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'nim',
        'alamat',
        'no_telepon',
        'tahun_masuk',
        'semester_aktif',
        'total_tunggakan',
        'status_mahasiswa',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'tahun_masuk' => 'integer',
        'semester_aktif' => 'integer',
        'total_tunggakan' => 'decimal:2',
    ];

    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    public function verifikasi_pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'verifikasi_oleh');
    }
}