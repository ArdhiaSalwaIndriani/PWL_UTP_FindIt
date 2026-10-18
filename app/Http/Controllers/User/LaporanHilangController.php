<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanHilangRequest;
use App\Models\Kategori;
use App\Models\LaporanHilang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanHilangController extends Controller
{
    // Menampilkan laporan milik pengguna yang sedang login
    public function index(Request $request)
    {
        $laporan = auth()->user()->laporanHilang()
            ->with('kategori')
            ->cari($request->q)
            ->dariKategori($request->kategori)
            ->when(
                $request->status,
                fn ($q, $status) => $q->where('status', $status)
            )
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('user.laporan-hilang.index', [
            'laporan' => $laporan,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // Form tambah laporan
    public function create()
    {
        return view('user.laporan-hilang.create', [
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // Menyimpan laporan baru
    public function store(LaporanHilangRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')
                ->store('foto-barang', 'public');
        }

        auth()->user()->laporanHilang()->create($data);

        return redirect()->route('laporan-hilang.index')
            ->with(
                'sukses',
                'Laporan berhasil dikirim dan menunggu verifikasi admin.'
            );
    }

    // Detail laporan
    public function show(LaporanHilang $laporanHilang)
    {
        $this->otorisasi($laporanHilang);

        $laporanHilang->load(['kategori', 'verifikator']);

        return view('user.laporan-hilang.show', [
            'laporan' => $laporanHilang,
        ]);
    }

    // Form edit laporan
    public function edit(LaporanHilang $laporanHilang)
    {
        $this->otorisasi($laporanHilang, true);

        return view('user.laporan-hilang.edit', [
            'laporan' => $laporanHilang,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    // Memperbarui laporan
    public function update(
        LaporanHilangRequest $request,
        LaporanHilang $laporanHilang
    ) {
        $this->otorisasi($laporanHilang, true);

        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($laporanHilang->foto) {
                Storage::disk('public')->delete($laporanHilang->foto);
            }

            $data['foto'] = $request->file('foto')
                ->store('foto-barang', 'public');
        }

        $laporanHilang->fill($data);
        $laporanHilang->forceFill([
            'status' => 'menunggu',
            'alasan_penolakan' => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ])->save();

        return redirect()->route(
            'laporan-hilang.show',
            $laporanHilang
        )->with('sukses', 'Laporan berhasil diperbarui.');
    }

    // Menghapus laporan
    public function destroy(LaporanHilang $laporanHilang)
    {
        $this->otorisasi($laporanHilang, true);

        if ($laporanHilang->foto) {
            Storage::disk('public')->delete($laporanHilang->foto);
        }

        $laporanHilang->delete();

        return redirect()->route('laporan-hilang.index')
            ->with('sukses', 'Laporan berhasil dihapus.');
    }

    // Memastikan pengguna hanya mengakses laporan miliknya
    private function otorisasi(LaporanHilang $laporanHilang, bool $untukUbah = false): void
    {
        abort_unless(
            $laporanHilang->pengguna_id === auth()->id(),
            403,
            'Anda tidak memiliki akses ke laporan ini.'
        );

        abort_if(
            $untukUbah && ! $laporanHilang->bisaDiubahPemilik(),
            403,
            'Laporan hanya dapat diubah atau dihapus saat menunggu atau ditolak.'
        );
    }
}
