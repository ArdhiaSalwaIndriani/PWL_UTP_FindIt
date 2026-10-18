<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class KategoriController extends Controller
{
    public function index()
    {
        $kategori = Kategori::withCount(['laporanHilang', 'laporanTemuan'])->orderBy('nama')->paginate(15);

        return view('admin.kategori.index', compact('kategori'));
    }

    public function create()
    {
        return view('admin.kategori.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:100', 'unique:kategori,nama']]);
        Kategori::create(['nama' => $data['nama'], 'slug' => Str::slug($data['nama'])]);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil ditambahkan.');
    }

    public function edit(Kategori $kategori)
    {
        return view('admin.kategori.edit', compact('kategori'));
    }

    public function update(Request $request, Kategori $kategori)
    {
        $data = $request->validate(['nama' => ['required', 'string', 'max:100', Rule::unique('kategori', 'nama')->ignore($kategori->id)]]);
        $kategori->update(['nama' => $data['nama'], 'slug' => Str::slug($data['nama'])]);

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil diperbarui.');
    }

    public function destroy(Kategori $kategori)
    {
        if ($kategori->laporanHilang()->exists() || $kategori->laporanTemuan()->exists()) {
            return back()->with('galat', 'Kategori masih dipakai oleh laporan sehingga tidak dapat dihapus.');
        }
        $kategori->delete();

        return redirect()->route('admin.kategori.index')->with('sukses', 'Kategori berhasil dihapus.');
    }
}
