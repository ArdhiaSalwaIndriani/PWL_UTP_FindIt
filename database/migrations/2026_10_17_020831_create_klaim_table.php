
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('klaim', function (Blueprint $table) {
            $table->id();

            $table->foreignId('laporan_temuan_id')
                ->constrained('laporan_temuan')
                ->cascadeOnDelete();

            $table->foreignId('pengguna_id')
                ->constrained('pengguna')
                ->cascadeOnDelete();

            $table->text('alasan_klaim');
            $table->text('ciri_khusus');
            $table->string('foto_bukti')->nullable();

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'dibatalkan'
            ])->default('menunggu');

            $table->text('alasan_penolakan')->nullable();

            $table->foreignId('diproses_oleh')
                ->nullable()
                ->constrained('pengguna')
                ->nullOnDelete();

            $table->timestamp('diproses_pada')->nullable();
            $table->timestamp('dikembalikan_pada')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index(['pengguna_id', 'status']);
            $table->index(['laporan_temuan_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('klaim');
    }
};
