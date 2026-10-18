<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanTemuanRequest;
use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanTemuan;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LaporanTemuanController extends Controller
{
    public function index(Request $request)
    {
        $laporan = auth()->user()->laporanTemuan()
            ->with('kategori')
            ->cari($request->q)
            ->dariKategori($request->kategori)
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->latest()->paginate(10)->withQueryString();

        return view('user.laporan-temuan.index', [
            'laporan' => $laporan,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function create()
    {
        return view('user.laporan-temuan.create', [
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function store(LaporanTemuanRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }

        auth()->user()->laporanTemuan()->create($data);

        return redirect()->route('laporan-temuan.index')
            ->with('sukses', 'Laporan berhasil dikirim dan menunggu verifikasi admin.');
    }

    public function show(LaporanTemuan $laporanTemuan)
    {
        $this->otorisasi($laporanTemuan);
        $laporanTemuan->load(['kategori', 'verifikator']);
        if ($laporanTemuan->pengguna_id === auth()->id()) {
            $laporanTemuan->load('klaim.pengklaim');
        }

        return view('user.laporan-temuan.show', ['laporan' => $laporanTemuan]);
    }

    public function konfirmasiKecocokan(LaporanTemuan $laporanTemuan, Klaim $klaim)
    {
        $this->otorisasi($laporanTemuan);
        abort_unless($klaim->laporan_temuan_id === $laporanTemuan->id, 404);

        if (! in_array($klaim->status, ['menunggu', 'disetujui'], true) || $klaim->dikonfirmasi_penemu_pada !== null) {
            return back()->with('galat', 'Klaim ini tidak lagi menunggu konfirmasi kecocokan.');
        }

        $klaim->forceFill(['dikonfirmasi_penemu_pada' => now()])->save();
        Notifikasi::kirim(
            $klaim->pengguna_id,
            'klaim_dikonfirmasi_penemu',
            'Penemu mengonfirmasi kecocokan',
            "Penemu mengonfirmasi bahwa ciri barang \"{$laporanTemuan->nama_barang}\" cocok dengan klaim Anda. Admin tetap akan memeriksa bukti kepemilikan.",
            $klaim
        );
        $this->beriTahuAdmin($klaim, 'Kecocokan klaim dikonfirmasi penemu', "Penemu mengonfirmasi kecocokan untuk barang \"{$laporanTemuan->nama_barang}\".");

        return back()->with('sukses', 'Kecocokan dicatat. Admin tetap memeriksa bukti sebelum memutuskan klaim.');
    }

    public function catatPenyerahan(LaporanTemuan $laporanTemuan, Klaim $klaim)
    {
        $this->otorisasi($laporanTemuan);
        abort_unless($klaim->laporan_temuan_id === $laporanTemuan->id, 404);

        if ($klaim->status !== 'disetujui' || $klaim->dikonfirmasi_penemu_pada === null || $klaim->diserahkan_penemu_pada !== null) {
            return back()->with('galat', 'Penyerahan hanya dapat dicatat setelah kecocokan dikonfirmasi dan admin menyetujui klaim.');
        }

        $klaim->forceFill(['diserahkan_penemu_pada' => now()])->save();
        Notifikasi::kirim(
            $klaim->pengguna_id,
            'klaim_barang_diserahkan',
            'Barang diserahkan penemu',
            "Penemu mencatat penyerahan barang \"{$laporanTemuan->nama_barang}\" kepada Anda. Admin akan mengonfirmasi penyelesaian.",
            $klaim
        );
        $this->beriTahuAdmin($klaim, 'Penemu mencatat penyerahan barang', "Penemu mencatat barang \"{$laporanTemuan->nama_barang}\" telah diserahkan kepada pengklaim.");

        return back()->with('sukses', 'Penyerahan dicatat. Admin dapat mengonfirmasi penyelesaian klaim.');
    }

    public function edit(LaporanTemuan $laporanTemuan)
    {
        $this->otorisasi($laporanTemuan, true);

        return view('user.laporan-temuan.edit', [
            'laporan' => $laporanTemuan,
            'daftarKategori' => Kategori::orderBy('nama')->get(),
        ]);
    }

    public function update(LaporanTemuanRequest $request, LaporanTemuan $laporanTemuan)
    {
        $this->otorisasi($laporanTemuan, true);
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            if ($laporanTemuan->foto) {
                Storage::disk('public')->delete($laporanTemuan->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }

        $laporanTemuan->fill($data);
        $laporanTemuan->forceFill([
            'status' => 'menunggu',
            'alasan_penolakan' => null,
            'diverifikasi_oleh' => null,
            'diverifikasi_pada' => null,
        ])->save();

        return redirect()->route('laporan-temuan.show', $laporanTemuan)
            ->with('sukses', 'Laporan berhasil diperbarui dan masuk antrean verifikasi.');
    }

    public function destroy(LaporanTemuan $laporanTemuan)
    {
        $this->otorisasi($laporanTemuan, true);
        if ($laporanTemuan->foto) {
            Storage::disk('public')->delete($laporanTemuan->foto);
        }
        $laporanTemuan->delete();

        return redirect()->route('laporan-temuan.index')->with('sukses', 'Laporan berhasil dihapus.');
    }

    private function otorisasi(LaporanTemuan $laporan, bool $untukUbah = false): void
    {
        abort_unless($laporan->pengguna_id === auth()->id(), 403);
        abort_if(
            $untukUbah && ! $laporan->bisaDiubahPemilik(),
            403,
            'Laporan hanya dapat diubah atau dihapus saat menunggu atau ditolak.'
        );
    }

    private function beriTahuAdmin(Klaim $klaim, string $judul, string $pesan): void
    {
        User::query()->where('peran', 'admin')->where('aktif', true)->pluck('id')->each(
            fn (int $adminId) => Notifikasi::kirim($adminId, 'klaim_bantuan_penemu', $judul, $pesan, $klaim)
        );
    }
}
