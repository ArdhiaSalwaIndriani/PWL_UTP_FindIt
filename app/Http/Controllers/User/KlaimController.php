<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanTemuan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class KlaimController extends Controller
{
    public function index(Request $request)
    {
        $klaim = auth()->user()->klaim()->with('laporanTemuan.kategori')
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('user.klaim.index', compact('klaim'));
    }

    public function tersedia(Request $request)
    {
        $barang = LaporanTemuan::with('kategori')->where('status', 'tersedia')
            ->where('pengguna_id', '!=', auth()->id())
            ->whereDoesntHave('klaim', fn ($query) => $query->where('status', 'disetujui'))
            ->cari($request->q)->dariKategori($request->kategori)
            ->latest()->paginate(12)->withQueryString();
        $sudahDiklaim = auth()->user()->klaim()->where('status', 'menunggu')
            ->pluck('laporan_temuan_id')->all();

        return view('user.klaim.tersedia', [
            'barang' => $barang,
            'sudahDiklaim' => $sudahDiklaim,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function create(LaporanTemuan $laporanTemuan)
    {
        if ($pesan = $this->alasanTidakBisaKlaim($laporanTemuan)) {
            return redirect()->route('klaim.tersedia')->with('galat', $pesan);
        }

        return view('user.klaim.create', ['barang' => $laporanTemuan->load('kategori')]);
    }

    public function store(Request $request, LaporanTemuan $laporanTemuan)
    {
        if ($pesan = $this->alasanTidakBisaKlaim($laporanTemuan)) {
            return redirect()->route('klaim.tersedia')->with('galat', $pesan);
        }

        $data = $request->validate([
            'alasan_klaim' => ['required', 'string', 'min:10'],
            'ciri_khusus' => ['required', 'string', 'min:10'],
            'foto_bukti' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);
        if ($request->hasFile('foto_bukti')) {
            $data['foto_bukti'] = $request->file('foto_bukti')->store('foto-bukti', 'public');
        }
        $data['laporan_temuan_id'] = $laporanTemuan->id;
        $klaim = auth()->user()->klaim()->create($data);
        Notifikasi::kirim(
            $laporanTemuan->pengguna_id,
            'klaim_diajukan',
            'Ada pengajuan klaim',
            auth()->user()->nama." mengajukan klaim untuk barang \"{$laporanTemuan->nama_barang}\". Periksa ciri barang pada laporan Anda.",
            $klaim
        );

        return redirect()->route('klaim.index')->with('sukses', 'Klaim berhasil diajukan dan menunggu pemeriksaan admin.');
    }

    public function show(Klaim $klaim)
    {
        abort_unless($klaim->pengguna_id === auth()->id(), 403);
        $klaim->load(['laporanTemuan.kategori', 'pemroses']);

        return view('user.klaim.show', compact('klaim'));
    }

    private function alasanTidakBisaKlaim(LaporanTemuan $barang): ?string
    {
        if ($barang->status !== 'tersedia') {
            return 'Barang ini tidak lagi tersedia untuk diklaim.';
        }
        if ($barang->pengguna_id === auth()->id()) {
            return 'Anda tidak dapat mengklaim barang temuan sendiri.';
        }
        if ($barang->klaim()->where('status', 'disetujui')->exists()) {
            return 'Klaim untuk barang ini sudah disetujui dan sedang menunggu penyerahan.';
        }
        if (auth()->user()->klaim()->where('laporan_temuan_id', $barang->id)->where('status', 'menunggu')->exists()) {
            return 'Klaim untuk barang ini masih menunggu pemeriksaan.';
        }

        return null;
    }
}
