
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifikasi', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengguna_id')
                ->constrained('pengguna')
                ->cascadeOnDelete();

            $table->string('jenis');
            $table->string('judul');
            $table->text('pesan');

            $table->nullableMorphs('terkait');

            $table->timestamp('dibaca_pada')->nullable();
            $table->timestamps();

            $table->index(['pengguna_id', 'dibaca_pada']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifikasi');
    }
};
