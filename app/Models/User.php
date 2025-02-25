<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

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
        'foto'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relasi ke tagihan
    public function tagihan()
    {
        return $this->hasMany(Tagihan::class);
    }

    // Relasi ke pembayaran
    public function pembayaran()
    {
        return $this->hasManyThrough(Pembayaran::class, Tagihan::class);
    }

    public function verifikasi_pembayaran()
    {
        return $this->hasMany(Pembayaran::class, 'verifikasi_oleh');
    }
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }
}