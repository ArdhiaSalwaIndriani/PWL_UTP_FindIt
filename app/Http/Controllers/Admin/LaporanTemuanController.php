<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanTemuanRequest;
use App\Models\Kategori;
use App\Models\LaporanTemuan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanTemuanController extends Controller
{
    public function index(Request $request)
    {
        $laporan = LaporanTemuan::with(['pelapor', 'kategori'])
            ->when($request->q, function ($query, $kata) {
                $query->where(fn ($q) => $q->where('nama_barang', 'ilike', "%{$kata}%")
                    ->orWhereHas('pelapor', fn ($pelapor) => $pelapor->where('nama', 'ilike', "%{$kata}%")));
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->dariKategori($request->kategori)->latest()->paginate(15)->withQueryString();

        return view('admin.laporan-temuan.index', ['laporan' => $laporan, 'daftarKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function show(LaporanTemuan $laporanTemuan)
    {
        return view('admin.laporan-temuan.show', ['laporan' => $laporanTemuan->load(['pelapor', 'kategori', 'verifikator', 'klaim.pengklaim'])]);
    }

    public function edit(LaporanTemuan $laporanTemuan)
    {
        return view('admin.laporan-temuan.edit', ['laporan' => $laporanTemuan, 'daftarKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function update(LaporanTemuanRequest $request, LaporanTemuan $laporanTemuan)
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            if ($laporanTemuan->foto) {
                Storage::disk('public')->delete($laporanTemuan->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }
        $laporanTemuan->update($data);

        return redirect()->route('admin.laporan-temuan.show', $laporanTemuan)->with('sukses', 'Data laporan berhasil diperbarui.');
    }

    public function destroy(LaporanTemuan $laporanTemuan)
    {
        if ($laporanTemuan->foto) {
            Storage::disk('public')->delete($laporanTemuan->foto);
        }
        $laporanTemuan->delete();

        return redirect()->route('admin.laporan-temuan.index')->with('sukses', 'Laporan berhasil dihapus.');
    }

    public function setujui(LaporanTemuan $laporanTemuan)
    {
        abort_if($laporanTemuan->status === 'dikembalikan', 403);
        $laporanTemuan->forceFill(['status' => 'tersedia', 'alasan_penolakan' => null, 'diverifikasi_oleh' => auth()->id(), 'diverifikasi_pada' => now()])->save();
        Notifikasi::kirim($laporanTemuan->pengguna_id, 'laporan_temuan_disetujui', 'Laporan temuan disetujui', "Laporan barang temuan \"{$laporanTemuan->nama_barang}\" telah disetujui admin.", $laporanTemuan);

        return back()->with('sukses', 'Laporan disetujui. Barang kini tersedia untuk diklaim.');
    }

    public function tolak(Request $request, LaporanTemuan $laporanTemuan)
    {
        abort_if($laporanTemuan->status === 'dikembalikan', 403);
        $data = $request->validate(['alasan_penolakan' => ['required', 'string', 'max:500']]);
        $laporanTemuan->forceFill(['status' => 'ditolak', 'alasan_penolakan' => $data['alasan_penolakan'], 'diverifikasi_oleh' => auth()->id(), 'diverifikasi_pada' => now()])->save();
        Notifikasi::kirim($laporanTemuan->pengguna_id, 'laporan_temuan_ditolak', 'Laporan temuan ditolak', "Laporan \"{$laporanTemuan->nama_barang}\" ditolak. Alasan: {$data['alasan_penolakan']}", $laporanTemuan);

        return back()->with('sukses', 'Laporan ditolak.');
    }

    public function ubahStatus(Request $request, LaporanTemuan $laporanTemuan)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['tersedia', 'dikembalikan'])]]);
        $laporanTemuan->forceFill(['status' => $data['status'], 'dikembalikan_pada' => $data['status'] === 'dikembalikan' ? now() : null])->save();

        return back()->with('sukses', 'Status barang diperbarui.');
    }
}
