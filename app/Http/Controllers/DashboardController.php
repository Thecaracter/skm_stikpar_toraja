<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Pembayaran;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        if (auth()->user()->role === 'admin') {
            // Statistik untuk Admin
            $total_mahasiswa = User::where('role', 'mahasiswa')->count();
            $mahasiswa_aktif = User::where('role', 'mahasiswa')
                ->where('status_mahasiswa', 'aktif')
                ->count();

            $total_tagihan = Tagihan::sum('jumlah_tagihan');
            $total_terbayar = Tagihan::sum('jumlah_terbayar');

            // Pembayaran yang perlu diverifikasi
            $pending_verifikasi = Pembayaran::where('status', 'menunggu')
                ->with(['tagihan.user', 'tagihan.jenis_pembayaran'])
                ->latest()
                ->take(5)
                ->get();

            // 5 Mahasiswa dengan tunggakan terbesar
            $mahasiswa_tunggakan = User::where('role', 'mahasiswa')
                ->where('total_tunggakan', '>', 0)
                ->orderBy('total_tunggakan', 'desc')
                ->take(5)
                ->get();

            return view('pages.dashboard', compact(
                'total_mahasiswa',
                'mahasiswa_aktif',
                'total_tagihan',
                'total_terbayar',
                'pending_verifikasi',
                'mahasiswa_tunggakan'
            ));
        }

        // Untuk Mahasiswa (kode yang sudah ada)
        $tagihan_terbaru = Tagihan::with('jenis_pembayaran')
            ->where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        $pembayaran_count = Pembayaran::whereHas('tagihan', function ($query) {
            $query->where('user_id', auth()->id());
        })->where('status', 'terverifikasi')->count();

        return view('pages.dashboard', compact('tagihan_terbaru', 'pembayaran_count'));
    }
}
