<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Barang',
            'Kendaraan',
            'Orang',
            'Hewan',
        ];

        foreach ($kategori as $nama) {
            Kategori::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($nama)],
                ['nama' => $nama]
            );
        }
    }
}
