<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait PencarianBarang
{
    public function scopeCari(Builder $query, ?string $kata): Builder
    {
        return $query->when(
            $kata,
            fn ($q) => $q->where('nama_barang', 'ilike', "%{$kata}%")
        );
    }

    public function scopeDariKategori(Builder $query, $kategoriId): Builder
    {
        return $query->when(
            $kategoriId,
            fn ($q) => $q->where('kategori_id', $kategoriId)
        );
    }
}
