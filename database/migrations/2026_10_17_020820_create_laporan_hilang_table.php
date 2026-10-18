
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('laporan_hilang', function (Blueprint $table) {
            $table->id();

            $table->foreignId('pengguna_id')
                ->constrained('pengguna')
                ->cascadeOnDelete();

            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->restrictOnDelete();

            $table->string('nama_barang');
            $table->string('foto')->nullable();
            $table->text('deskripsi');
            $table->string('lokasi_kehilangan');
            $table->date('tanggal_kehilangan');

            $table->enum('status', [
                'menunggu',
                'disetujui',
                'ditolak',
                'selesai'
            ])->default('menunggu');

            $table->text('alasan_penolakan')->nullable();

            $table->foreignId('diverifikasi_oleh')
                ->nullable()
                ->constrained('pengguna')
                ->nullOnDelete();

            $table->timestamp('diverifikasi_pada')->nullable();

            $table->timestamps();

            $table->index('status');
            $table->index('nama_barang');
            $table->index(['pengguna_id', 'status']);
            $table->index(['kategori_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('laporan_hilang');
    }
};
