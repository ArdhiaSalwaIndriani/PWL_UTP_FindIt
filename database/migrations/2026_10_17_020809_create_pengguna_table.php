
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('email')->unique();
            $table->string('kata_sandi');
            $table->enum('peran', ['user', 'admin'])->default('user');
            $table->boolean('aktif')->default(true);
            $table->string('foto_profil')->nullable();
            $table->rememberToken();
            $table->timestamps();

            $table->index('peran');
            $table->index('aktif');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
};
