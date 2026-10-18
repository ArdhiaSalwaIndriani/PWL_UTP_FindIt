<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Klaim extends Model
{
    protected $table = 'klaim';

    protected $fillable = [
        'laporan_temuan_id',
        'alasan_klaim',
        'ciri_khusus',
        'foto_bukti',
    ];

    protected function casts(): array
    {
        return [
            'diproses_pada' => 'datetime',
            'dikembalikan_pada' => 'datetime',
            'dikonfirmasi_penemu_pada' => 'datetime',
            'diserahkan_penemu_pada' => 'datetime',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function pengklaim()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function laporanTemuan()
    {
        return $this->belongsTo(LaporanTemuan::class, 'laporan_temuan_id');
    }

    public function pemroses()
    {
        return $this->belongsTo(User::class, 'diproses_oleh');
    }
}
