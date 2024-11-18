<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@gmail.com',
            'nim' => 'ADMIN001',
            'alamat' => 'Jl. Admin No. 1',
            'no_telepon' => '08123456789',
            'tahun_masuk' => 2024,
            'semester_aktif' => 1,
            'status_mahasiswa' => 'aktif',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);
        User::create([
            'name' => 'Rizqi',
            'email' => 'semuamana@gmail.com',
            'nim' => 'ADMIN002',
            'alamat' => 'Jl. Admin No. 1',
            'no_telepon' => '08123456831',
            'tahun_masuk' => 2024,
            'semester_aktif' => 0,
            'status_mahasiswa' => 'aktif',
            'role' => 'admin',
            'password' => Hash::make('password'),
        ]);

        // Create Sample Students
        for ($i = 1; $i <= 5; $i++) {
            User::create([
                'name' => "Mahasiswa $i",
                'email' => "mahasiswa$i@gmail.com",
                'nim' => "2024" . str_pad($i, 4, '0', STR_PAD_LEFT),
                'alamat' => "Jl. Mahasiswa No. $i",
                'no_telepon' => "08" . rand(100000000, 999999999),
                'tahun_masuk' => 2024,
                'semester_aktif' => 1,
                'status_mahasiswa' => 'aktif',
                'role' => 'mahasiswa',
                'password' => Hash::make('password'),
            ]);
        }
    }
}
