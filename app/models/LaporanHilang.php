<?php

namespace App\Models;

use App\Models\Concerns\PencarianBarang;
use Illuminate\Database\Eloquent\Model;

class LaporanHilang extends Model
{
    use PencarianBarang;

    protected $table = 'laporan_hilang';

    protected $fillable = [
        'kategori_id',
        'nama_barang',
        'foto',
        'deskripsi',
        'lokasi_kehilangan',
        'tanggal_kehilangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_kehilangan' => 'date',
            'diverifikasi_pada' => 'datetime',
        ];
    }

    public function pelapor()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }

    public function klaim()
    {
        return $this->hasMany(Klaim::class, 'laporan_hilang_id');
    }

    public function verifikator()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    public function bisaDiubahPemilik(): bool
    {
        return in_array($this->status, ['menunggu', 'ditolak'], true);
    }
}
