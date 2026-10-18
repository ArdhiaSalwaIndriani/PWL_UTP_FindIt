<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function formDaftar()
    {
        return view('auth.daftar');
    }

    public function daftar(Request $request)
    {
        // Input konfirmasi harus bernama kata_sandi_confirmation
        $data = $request->validate([
            'nama'       => ['required', 'string', 'max:255'],
            'email'      => ['required', 'email', 'max:255', 'unique:pengguna,email'],
            'kata_sandi' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        // Peran otomatis "user" dari default database
        $pengguna = User::create($data);

        Auth::login($pengguna);
        $request->session()->regenerate();

        return redirect()->route('beranda');
    }

    public function formMasuk()
    {
        return view('auth.masuk');
    }

    public function masuk(Request $request)
    {
        $data = $request->validate([
            'email'      => ['required', 'email'],
            'kata_sandi' => ['required'],
        ]);

        // Laravel memakai kunci "password" untuk autentikasi
        $berhasil = Auth::attempt([
            'email'    => $data['email'],
            'password' => $data['kata_sandi'],
            'aktif'    => true,
        ], $request->boolean('ingat_saya'));

        if (! $berhasil) {
            $pengguna = User::where('email', $data['email'])->first();

            $pesan = ($pengguna && ! $pengguna->aktif)
                ? 'Akun Anda dinonaktifkan. Silakan hubungi admin.'
                : 'Email atau kata sandi salah.';

            return back()
                ->withErrors(['email' => $pesan])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->route(
            Auth::user()->adalahAdmin() ? 'admin.dashboard' : 'beranda'
        );
    }

    public function keluar(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('masuk');
    }
}
