<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;

class KategoriSeeder extends Seeder
{
    public function run(): void
    {
        $kategori = [
            'Elektronik',
            'Dompet',
            'Dokumen',
            'Kunci',
            'Pakaian',
            'Alat Tulis',
        ];

        foreach ($kategori as $nama) {
            Kategori::firstOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($nama)],
                ['nama' => $nama]
            );
        }
    }
}
