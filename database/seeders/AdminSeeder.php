<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrNew(['email' => 'admin@findit.test']);
        $admin->forceFill([
            'nama' => 'Administrator',
            'kata_sandi' => 'admin12345',
            'peran' => 'admin',
            'aktif' => true,
        ])->save();
    }
}
