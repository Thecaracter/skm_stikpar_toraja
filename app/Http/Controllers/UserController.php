<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\QueryException;
use Dotenv\Exception\ValidationException;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    public function index()
    {
        $users = User::paginate(10);
        return view('pages.user', compact('users'));
    }
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'nim' => 'required|string|unique:users',
                'alamat' => 'required|string',
                'no_telepon' => 'required|string',
                'tahun_masuk' => 'required|string',
                'role' => 'required|in:admin,mahasiswa',
                'password' => ['required', Password::defaults()],
            ]);

            $validated['password'] = Hash::make($validated['password']);

            User::create($validated);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil ditambahkan');

        } catch (Exception $e) {
            Log::error('Error creating user: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }

    public function update(Request $request, User $user)
    {
        try {
            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'alamat' => 'sometimes|required|string',
                'no_telepon' => 'sometimes|required|string',
                'tahun_masuk' => 'sometimes|required|string',
                'role' => 'required|in:admin,mahasiswa',
                'password' => ['nullable', 'confirmed', Password::defaults()],
            ]);

            // Email validation with unique check excluding current user
            if ($request->has('email')) {
                $request->validate([
                    'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
                ]);
                $validated['email'] = $request->email;
            }

            // NIM validation with unique check excluding current user
            if ($request->has('nim')) {
                $request->validate([
                    'nim' => 'required|string|unique:users,nim,' . $user->id,
                ]);
                $validated['nim'] = $request->nim;
            }

            if (!empty($validated['password'])) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);

            return redirect()->route('users.index')
                ->with('success', 'User berhasil diperbarui');

        } catch (Exception $e) {
            Log::error('Error updating user: ' . $e->getMessage());
            return redirect()->back()
                ->withErrors($e->getMessage())
                ->withInput();
        }
    }
    public function destroy(User $user)
    {
        try {
            DB::beginTransaction();

            // 1. Dapatkan semua tagihan user
            $tagihanIds = $user->tagihan->pluck('id');

            // 2. Force delete semua pembayaran yang terkait dengan tagihan
            Pembayaran::whereIn('tagihan_id', $tagihanIds)->forceDelete();

            // 3. Force delete semua tagihan user
            $user->tagihan()->forceDelete();

            // 4. Set null untuk pembayaran yang diverifikasi oleh user ini
            Pembayaran::where('verifikasi_oleh', $user->id)->update(['verifikasi_oleh' => null]);

            // 5. Hapus user
            $user->delete();

            DB::commit();

            return redirect()->route('users.index')
                ->with('success', 'User dan semua data terkait berhasil dihapus');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Error deleting user: ' . $e->getMessage());

            return redirect()->route('users.index')
                ->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

}
