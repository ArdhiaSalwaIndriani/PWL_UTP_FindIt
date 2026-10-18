<?php

namespace App\Models;

use App\Models\Concerns\PencarianBarang;
use Illuminate\Database\Eloquent\Model;

class LaporanTemuan extends Model
{
    use PencarianBarang;

    protected $table = 'laporan_temuan';

    protected $fillable = [
        'kategori_id',
        'nama_barang',
        'foto',
        'deskripsi',
        'lokasi_ditemukan',
        'tanggal_ditemukan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_ditemukan' => 'date',
            'diverifikasi_pada' => 'datetime',
            'dikembalikan_pada' => 'datetime',
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
        return $this->hasMany(Klaim::class, 'laporan_temuan_id');
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
