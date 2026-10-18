<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CekPeran
{
    // Dipakai: middleware('peran:user') atau middleware('peran:admin')
    // Harus dipasang setelah middleware 'auth'
    public function handle(Request $request, Closure $next, string $peran): Response
    {
        $pengguna = $request->user();

        // Akun yang dinonaktifkan langsung dikeluarkan
        if (! $pengguna->aktif) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('masuk')
                ->withErrors([
                    'email' => 'Akun Anda dinonaktifkan. Silakan hubungi admin.'
                ]);
        }

        // User tidak bisa membuka halaman admin, dan sebaliknya
        if ($pengguna->peran !== $peran) {
            return redirect()
                ->route($pengguna->adalahAdmin() ? 'admin.dashboard' : 'beranda')
                ->with('galat', 'Anda tidak memiliki akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
