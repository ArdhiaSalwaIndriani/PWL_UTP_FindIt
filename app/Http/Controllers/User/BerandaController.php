<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\LaporanHilang;
use App\Models\LaporanTemuan;
use Illuminate\Http\Request;

class BerandaController extends Controller
{
    public function index(Request $request)
    {
        $pengguna = $request->user();
        $laporanHilangSaya = $pengguna->laporanHilang()
            ->with('kategori')
            ->latest()
            ->take(8)
            ->get();
        $laporanTemuanSaya = $pengguna->laporanTemuan()
            ->with('kategori')
            ->latest()
            ->take(8)
            ->get();
        $kategoriDicari = $pengguna->laporanHilang()
            ->select('kategori_id')
            ->distinct()
            ->pluck('kategori_id');

        return view('user.beranda', [
            'laporanHilangSaya' => $laporanHilangSaya,
            'laporanTemuanSaya' => $laporanTemuanSaya,
            'temuanRelevan' => LaporanTemuan::with(['kategori', 'pelapor'])
                ->where('pengguna_id', '!=', $pengguna->id)
                ->whereDoesntHave('klaim', fn ($query) => $query->where('status', 'disetujui'))
                ->where(function ($query) use ($kategoriDicari) {
                    if ($kategoriDicari->isNotEmpty()) {
                        $query->whereIn('status', ['menunggu', 'tersedia'])
                            ->whereIn('kategori_id', $kategoriDicari);
                    } else {
                        $query->where('status', 'tersedia');
                    }
                })
                ->cari($request->q)
                ->dariKategori($request->kategori)
                ->latest()
                ->take(8)
                ->get(),
            'hilangTerbaru' => LaporanHilang::with(['kategori', 'pelapor'])
                ->where('status', 'disetujui')
                ->cari($request->q)
                ->dariKategori($request->kategori)
                ->latest()->take(8)->get(),
            'temuanTerbaru' => LaporanTemuan::with(['kategori', 'pelapor'])
                ->where('status', 'tersedia')
                ->whereDoesntHave('klaim', fn ($query) => $query->where('status', 'disetujui'))
                ->cari($request->q)
                ->dariKategori($request->kategori)
                ->latest()->take(8)->get(),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function jelajahHilang(Request $request)
    {
        return view('user.jelajah.hilang', [
            'laporan' => LaporanHilang::with(['kategori', 'pelapor'])
                ->where('status', 'disetujui')
                ->cari($request->q)->dariKategori($request->kategori)
                ->latest()->paginate(12)->withQueryString(),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function jelajahTemuan(Request $request)
    {
        return view('user.jelajah.temuan', [
            'laporan' => LaporanTemuan::with(['kategori', 'pelapor'])
                ->where('status', 'tersedia')->where('pengguna_id', '!=', auth()->id())
                ->whereDoesntHave('klaim', fn ($query) => $query->where('status', 'disetujui'))
                ->cari($request->q)->dariKategori($request->kategori)
                ->latest()->paginate(12)->withQueryString(),
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function detailHilang(LaporanHilang $laporanHilang)
    {
        abort_unless($laporanHilang->status === 'disetujui' || $laporanHilang->pengguna_id === auth()->id(), 404);
        $laporanHilang->load(['kategori', 'pelapor']);

        return view('user.jelajah.detail-hilang', ['laporan' => $laporanHilang]);
    }

    public function detailTemuan(LaporanTemuan $laporanTemuan)
    {
        $pengguna = auth()->user();
        $dapatMelihatTemuanMenunggu = $laporanTemuan->status === 'menunggu'
            && $pengguna->laporanHilang()->where('kategori_id', $laporanTemuan->kategori_id)->exists();

        abort_unless(
            $laporanTemuan->pengguna_id === $pengguna->id
                || $dapatMelihatTemuanMenunggu
                || ($laporanTemuan->status === 'tersedia' && ! $laporanTemuan->klaim()->where('status', 'disetujui')->exists()),
            404
        );
        $laporanTemuan->load(['kategori', 'pelapor']);

        return view('user.jelajah.detail-temuan', ['laporan' => $laporanTemuan]);
    }
}
