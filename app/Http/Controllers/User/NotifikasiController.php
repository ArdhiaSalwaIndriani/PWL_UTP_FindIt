<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class NotifikasiController extends Controller
{
    public function index(Request $request)
    {
        $notifikasi = auth()->user()->notifikasi()
            ->when($request->boolean('belum'), fn ($query) => $query->belumDibaca())
            ->latest()->paginate(15)->withQueryString();

        return view('user.notifikasi.index', compact('notifikasi'));
    }

    public function tandaiDibaca(Notifikasi $notifikasi)
    {
        abort_unless($notifikasi->pengguna_id === auth()->id(), 403);
        if (! $notifikasi->sudahDibaca()) {
            $notifikasi->update(['dibaca_pada' => now()]);
        }

        return back();
    }

    public function tandaiSemuaDibaca()
    {
        auth()->user()->notifikasi()->belumDibaca()->update(['dibaca_pada' => now()]);

        return back()->with('sukses', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
