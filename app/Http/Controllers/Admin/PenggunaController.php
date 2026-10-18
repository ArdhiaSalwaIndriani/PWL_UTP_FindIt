<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PenggunaController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = User::query()
            ->when($request->q, fn ($query, $kata) => $query->where(fn ($q) => $q->where('nama', 'ilike', "%{$kata}%")->orWhere('email', 'ilike', "%{$kata}%")))
            ->when($request->peran, fn ($query, $peran) => $query->where('peran', $peran))
            ->when($request->filled('aktif'), fn ($query) => $query->where('aktif', $request->boolean('aktif')))
            ->latest()->paginate(15)->withQueryString();

        return view('admin.pengguna.index', compact('pengguna'));
    }

    public function show(User $pengguna)
    {
        $pengguna->loadCount(['laporanHilang', 'laporanTemuan', 'klaim']);

        return view('admin.pengguna.show', compact('pengguna'));
    }

    public function edit(User $pengguna)
    {
        return view('admin.pengguna.edit', compact('pengguna'));
    }

    public function update(Request $request, User $pengguna)
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('pengguna', 'email')->ignore($pengguna->id)],
        ]);
        $pengguna->update($data);

        return redirect()->route('admin.pengguna.show', $pengguna)->with('sukses', 'Data akun berhasil diperbarui.');
    }

    public function ubahStatusAktif(User $pengguna)
    {
        abort_if($pengguna->id === auth()->id(), 403, 'Anda tidak dapat menonaktifkan akun Anda sendiri.');
        $pengguna->forceFill(['aktif' => ! $pengguna->aktif])->save();

        return back()->with('sukses', $pengguna->aktif ? 'Akun diaktifkan.' : 'Akun dinonaktifkan.');
    }
}
