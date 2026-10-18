<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klaim;
use App\Models\LaporanTemuan;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KlaimController extends Controller
{
    public function index(Request $request)
    {
        $klaim = Klaim::with(['laporanTemuan', 'pengklaim'])
            ->when($request->status, fn ($query, $status) => $query->where('status', $status))
            ->when($request->q, function ($query, $kata) {
                $query->where(fn ($q) => $q->whereHas('laporanTemuan', fn ($barang) => $barang->where('nama_barang', 'ilike', "%{$kata}%"))
                    ->orWhereHas('pengklaim', fn ($user) => $user->where('nama', 'ilike', "%{$kata}%")));
            })
            ->orderByRaw("CASE WHEN status = 'menunggu' THEN 0 ELSE 1 END")
            ->latest()->paginate(15)->withQueryString();

        return view('admin.klaim.index', compact('klaim'));
    }

    public function show(Klaim $klaim)
    {
        $klaim->load(['laporanTemuan.kategori', 'laporanTemuan.pelapor', 'pengklaim', 'pemroses']);

        return view('admin.klaim.show', compact('klaim'));
    }

    public function setujui(Klaim $klaim)
    {
        $galat = DB::transaction(function () use ($klaim) {
            $barang = LaporanTemuan::whereKey($klaim->laporan_temuan_id)->lockForUpdate()->firstOrFail();
            $klaim->refresh();

            if ($klaim->status !== 'menunggu') {
                return 'Klaim ini sudah diproses.';
            }
            if ($barang->status !== 'tersedia') {
                return 'Barang ini tidak lagi berstatus tersedia.';
            }
            if (Klaim::where('laporan_temuan_id', $barang->id)->where('status', 'disetujui')->exists()) {
                return 'Barang ini sudah memiliki klaim yang disetujui.';
            }

            $klaim->forceFill(['status' => 'disetujui', 'alasan_penolakan' => null, 'diproses_oleh' => auth()->id(), 'diproses_pada' => now()])->save();
            Notifikasi::kirim($klaim->pengguna_id, 'klaim_disetujui', 'Klaim disetujui', "Klaim Anda untuk barang \"{$barang->nama_barang}\" disetujui. Penemu akan membantu proses penyerahan.", $klaim);
            Notifikasi::kirim($barang->pengguna_id, 'klaim_disetujui', 'Klaim disetujui admin', "Klaim untuk barang \"{$barang->nama_barang}\" disetujui. Hubungi pengklaim untuk penyerahan barang.", $klaim);

            return null;
        });

        if ($galat !== null) {
            return back()->with('galat', $galat);
        }

        return back()->with('sukses', 'Klaim disetujui. Pengklaim dan penemu sudah diberi tahu.');
    }

    public function tolak(Request $request, Klaim $klaim)
    {
        if ($klaim->status !== 'menunggu') {
            return back()->with('galat', 'Klaim ini sudah diproses.');
        }
        $data = $request->validate(['alasan_penolakan' => ['required', 'string', 'max:500']]);
        DB::transaction(function () use ($klaim, $data) {
            $klaim->forceFill(['status' => 'ditolak', 'alasan_penolakan' => $data['alasan_penolakan'], 'diproses_oleh' => auth()->id(), 'diproses_pada' => now()])->save();
            Notifikasi::kirim($klaim->pengguna_id, 'klaim_ditolak', 'Klaim ditolak', "Klaim Anda untuk barang \"{$klaim->laporanTemuan->nama_barang}\" ditolak. Alasan: {$data['alasan_penolakan']}", $klaim);
            Notifikasi::kirim($klaim->laporanTemuan->pengguna_id, 'klaim_ditolak', 'Klaim ditolak admin', "Klaim untuk barang \"{$klaim->laporanTemuan->nama_barang}\" ditolak oleh admin. Barang belum diserahkan.", $klaim);
        });

        return back()->with('sukses', 'Klaim ditolak.');
    }

    public function konfirmasiPengembalian(Klaim $klaim)
    {
        if ($klaim->status !== 'disetujui' || $klaim->dikembalikan_pada !== null) {
            return back()->with('galat', 'Pengembalian hanya dapat dikonfirmasi untuk klaim disetujui yang belum dikembalikan.');
        }
        if ($klaim->diserahkan_penemu_pada === null) {
            return back()->with('galat', 'Penemu harus mencatat penyerahan barang sebelum admin menyelesaikan klaim.');
        }
        DB::transaction(function () use ($klaim) {
            $barang = $klaim->laporanTemuan;
            $klaim->forceFill(['dikembalikan_pada' => now()])->save();
            $barang->forceFill(['status' => 'dikembalikan', 'dikembalikan_pada' => now()])->save();
            Notifikasi::kirim(
                $klaim->pengguna_id,
                'klaim_barang_dikembalikan',
                'Penyerahan barang dikonfirmasi',
                "Admin mengonfirmasi barang \"{$barang->nama_barang}\" telah diserahkan kepada Anda.",
                $klaim
            );
            $lainnya = Klaim::where('laporan_temuan_id', $barang->id)->where('status', 'menunggu')->where('id', '!=', $klaim->id)->get();
            foreach ($lainnya as $lain) {
                $alasan = 'Barang sudah dikembalikan kepada pemilik yang sah.';
                $lain->forceFill(['status' => 'ditolak', 'alasan_penolakan' => $alasan, 'diproses_oleh' => auth()->id(), 'diproses_pada' => now()])->save();
                Notifikasi::kirim($lain->pengguna_id, 'klaim_ditolak', 'Klaim ditolak', "Klaim untuk barang \"{$barang->nama_barang}\" ditolak. {$alasan}", $lain);
            }
        });

        return back()->with('sukses', 'Pengembalian dikonfirmasi. Status barang kini dikembalikan.');
    }
}
