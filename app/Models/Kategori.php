<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    protected $table = 'kategori';

    protected $fillable = [
        'nama',
        'slug',
    ];

    public function laporanHilang()
    {
        return $this->hasMany(LaporanHilang::class, 'kategori_id');
    }

    public function laporanTemuan()
    {
        return $this->hasMany(LaporanTemuan::class, 'kategori_id');
    }
}
