<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\LaporanHilangRequest;
use App\Models\Kategori;
use App\Models\LaporanHilang;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class LaporanHilangController extends Controller
{
    public function index(Request $request)
    {
        $laporan = LaporanHilang::with(['pelapor', 'kategori'])
            ->when($request->q, function ($query, $kata) {
                $query->where(fn ($q) => $q->where('nama_barang', 'ilike', "%{$kata}%")
                    ->orWhereHas('pelapor', fn ($pelapor) => $pelapor->where('nama', 'ilike', "%{$kata}%")));
            })
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->dariKategori($request->kategori)->latest()->paginate(15)->withQueryString();

        return view('admin.laporan-hilang.index', ['laporan' => $laporan, 'daftarKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function show(LaporanHilang $laporanHilang)
    {
        return view('admin.laporan-hilang.show', ['laporan' => $laporanHilang->load(['pelapor', 'kategori', 'verifikator'])]);
    }

    public function edit(LaporanHilang $laporanHilang)
    {
        return view('admin.laporan-hilang.edit', ['laporan' => $laporanHilang, 'daftarKategori' => Kategori::orderBy('nama')->get()]);
    }

    public function update(LaporanHilangRequest $request, LaporanHilang $laporanHilang)
    {
        $data = $request->validated();
        if ($request->hasFile('foto')) {
            if ($laporanHilang->foto) {
                Storage::disk('public')->delete($laporanHilang->foto);
            }
            $data['foto'] = $request->file('foto')->store('foto-barang', 'public');
        }
        $laporanHilang->update($data);

        return redirect()->route('admin.laporan-hilang.show', $laporanHilang)->with('sukses', 'Data laporan berhasil diperbarui.');
    }

    public function destroy(LaporanHilang $laporanHilang)
    {
        if ($laporanHilang->foto) {
            Storage::disk('public')->delete($laporanHilang->foto);
        }
        $laporanHilang->delete();

        return redirect()->route('admin.laporan-hilang.index')->with('sukses', 'Laporan berhasil dihapus.');
    }

    public function setujui(LaporanHilang $laporanHilang)
    {
        abort_if($laporanHilang->status === 'selesai', 403);
        $laporanHilang->forceFill(['status' => 'disetujui', 'alasan_penolakan' => null, 'diverifikasi_oleh' => auth()->id(), 'diverifikasi_pada' => now()])->save();
        Notifikasi::kirim($laporanHilang->pengguna_id, 'laporan_hilang_disetujui', 'Laporan disetujui', "Laporan barang hilang \"{$laporanHilang->nama_barang}\" telah disetujui admin.", $laporanHilang);

        return back()->with('sukses', 'Laporan disetujui.');
    }

    public function tolak(Request $request, LaporanHilang $laporanHilang)
    {
        abort_if($laporanHilang->status === 'selesai', 403);
        $data = $request->validate(['alasan_penolakan' => ['required', 'string', 'max:500']]);
        $laporanHilang->forceFill(['status' => 'ditolak', 'alasan_penolakan' => $data['alasan_penolakan'], 'diverifikasi_oleh' => auth()->id(), 'diverifikasi_pada' => now()])->save();
        Notifikasi::kirim($laporanHilang->pengguna_id, 'laporan_hilang_ditolak', 'Laporan ditolak', "Laporan \"{$laporanHilang->nama_barang}\" ditolak. Alasan: {$data['alasan_penolakan']}", $laporanHilang);

        return back()->with('sukses', 'Laporan ditolak.');
    }

    public function ubahStatus(Request $request, LaporanHilang $laporanHilang)
    {
        $data = $request->validate(['status' => ['required', Rule::in(['menunggu', 'selesai'])]]);
        $laporanHilang->forceFill(['status' => $data['status']])->save();

        return back()->with('sukses', 'Status laporan diperbarui.');
    }
}
