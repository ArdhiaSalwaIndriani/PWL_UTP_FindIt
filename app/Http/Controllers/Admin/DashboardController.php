<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Klaim;
use App\Models\LaporanHilang;
use App\Models\LaporanTemuan;

class DashboardController extends Controller
{
    public function index()
    {
        return view('admin.dashboard', [
            'jumlahHilang' => LaporanHilang::count(),
            'jumlahTemuan' => LaporanTemuan::count(),
            'jumlahKlaim' => Klaim::count(),
            'menungguHilang' => LaporanHilang::where('status', 'menunggu')->count(),
            'menungguTemuan' => LaporanTemuan::where('status', 'menunggu')->count(),
            'klaimBelumDiproses' => Klaim::where('status', 'menunggu')->count(),
            'barangDikembalikan' => LaporanTemuan::where('status', 'dikembalikan')->count(),
            'hilangTerbaru' => LaporanHilang::with(['pelapor', 'kategori'])->latest()->take(5)->get(),
            'temuanTerbaru' => LaporanTemuan::with(['pelapor', 'kategori'])->latest()->take(5)->get(),
        ]);
    }
}
