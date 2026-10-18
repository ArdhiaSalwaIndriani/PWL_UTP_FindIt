<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    // Notifiable tidak digunakan karena memakai tabel notifikasi sendiri.
    protected $table = 'pengguna';

    protected $fillable = [
        'nama',
        'email',
        'kata_sandi',
        'foto_profil',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'kata_sandi' => 'hashed',
            'aktif' => 'boolean',
        ];
    }

    public function getAuthPasswordName()
    {
        return 'kata_sandi';
    }

    public function getAuthPassword()
    {
        return $this->kata_sandi;
    }

    public function adalahAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function laporanHilang()
    {
        return $this->hasMany(LaporanHilang::class, 'pengguna_id');
    }

    public function laporanTemuan()
    {
        return $this->hasMany(LaporanTemuan::class, 'pengguna_id');
    }

    public function klaim()
    {
        return $this->hasMany(Klaim::class, 'pengguna_id');
    }

    public function notifikasi()
    {
        return $this->hasMany(Notifikasi::class, 'pengguna_id');
    }
}
