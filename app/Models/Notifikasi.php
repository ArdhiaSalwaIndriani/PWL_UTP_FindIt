<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notifikasi extends Model
{
    protected $table = 'notifikasi';

    protected $fillable = [
        'pengguna_id',
        'jenis',
        'judul',
        'pesan',
        'terkait_type',
        'terkait_id',
        'dibaca_pada',
    ];

    protected function casts(): array
    {
        return [
            'dibaca_pada' => 'datetime',
        ];
    }

    public function pengguna()
    {
        return $this->belongsTo(User::class, 'pengguna_id');
    }

    public function terkait(): MorphTo
    {
        return $this->morphTo('terkait');
    }

    public function scopeBelumDibaca(Builder $query): Builder
    {
        return $query->whereNull('dibaca_pada');
    }

    public function sudahDibaca(): bool
    {
        return $this->dibaca_pada !== null;
    }

    public static function kirim(int $penggunaId, string $jenis, string $judul, string $pesan, ?Model $terkait = null): self
    {
        return static::create([
            'pengguna_id' => $penggunaId,
            'jenis' => $jenis,
            'judul' => $judul,
            'pesan' => $pesan,
            'terkait_type' => $terkait?->getMorphClass(),
            'terkait_id' => $terkait?->getKey(),
        ]);
    }
}
