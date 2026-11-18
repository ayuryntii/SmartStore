<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Services\ActivityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'kata_sandi' => 'required|string',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'kata_sandi.required' => 'Kata sandi wajib diisi.',
        ]);

        $email = strtolower(trim($credentials['email']));
        $pengguna = Pengguna::where('email', $email)
            ->orWhere('email', str_replace('@warungku.id', '@bawara.id', $email))
            ->first();

        if (!$pengguna || !Hash::check($credentials['kata_sandi'], $pengguna->kata_sandi)) {
            return back()->withInput($request->only('email'))->with('error', 'Email atau kata sandi salah.');
        }

        if (!$pengguna->status_aktif) {
            return back()->withInput($request->only('email'))->with('error', 'Akun Anda dinonaktifkan. Hubungi Administrator.');
        }

        Auth::login($pengguna, $request->boolean('remember'));
        $request->session()->regenerate();

        ActivityLogService::log('login', 'pengguna', $pengguna->id, "Pengguna {$pengguna->nama} berhasil login.");

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang kembali, {$pengguna->nama}!");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            ActivityLogService::log('logout', 'pengguna', $user->id, "Pengguna {$user->nama} logout.");
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah berhasil keluar.');
    }
}
