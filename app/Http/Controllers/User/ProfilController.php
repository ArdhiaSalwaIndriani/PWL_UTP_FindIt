<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfilController extends Controller
{
    public function index()
    {
        $pengguna = auth()->user();

        return view($pengguna->adalahAdmin() ? 'admin.profil' : 'user.profil', compact('pengguna'));
    }

    public function perbarui(Request $request)
    {
        $pengguna = $request->user();
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($pengguna->id)],
            'foto_profil' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        if ($request->hasFile('foto_profil')) {
            if ($pengguna->foto_profil) {
                Storage::disk('public')->delete($pengguna->foto_profil);
            }
            $data['foto_profil'] = $request->file('foto_profil')->store('foto-profil', 'public');
        }
        $pengguna->update($data);

        return back()->with('sukses', 'Profil berhasil diperbarui.');
    }

    public function ubahKataSandi(Request $request)
    {
        $request->validateWithBag('kataSandi', [
            'kata_sandi_lama' => ['required', 'current_password'],
            'kata_sandi_baru' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $request->user()->update(['kata_sandi' => $request->kata_sandi_baru]);

        return back()->with('sukses', 'Kata sandi berhasil diubah.');
    }
}
