<?php

namespace Tests\Feature;

use App\Models\Kategori;
use App\Models\Klaim;
use App\Models\LaporanHilang;
use App\Models\LaporanTemuan;
use App\Models\User;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Tests\TestCase;

class ReportAndClaimWorkflowTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_user_dashboard_shows_personal_reports_and_matching_findings(): void
    {
        $pengguna = $this->buatPengguna('Rani');
        $penemu = $this->buatPengguna('Zahira');
        $kategori = $this->buatKategori('Kunci');
        $kategoriLain = $this->buatKategori('Elektronik');

        $laporanHilang = $this->buatLaporanHilang($pengguna, $kategori, 'Kunci Rani');
        $laporanTemuanSaya = $this->buatLaporanTemuan($pengguna, $kategoriLain, 'Kabel saya');
        $temuanMenungguCocok = $this->buatLaporanTemuan($penemu, $kategori, 'Kunci baru ditemukan');
        $temuanCocok = $this->buatLaporanTemuan($penemu, $kategori, 'Kunci ditemukan', 'tersedia');
        $temuanTidakCocok = $this->buatLaporanTemuan($penemu, $kategoriLain, 'Kamera ditemukan', 'tersedia');

        $response = $this->actingAs($pengguna)->get(route('beranda'));

        $response->assertOk()
            ->assertViewHas('laporanHilangSaya', fn ($laporan) => $laporan->contains('id', $laporanHilang->id))
            ->assertViewHas('laporanTemuanSaya', fn ($laporan) => $laporan->contains('id', $laporanTemuanSaya->id))
            ->assertViewHas('temuanRelevan', fn ($laporan) => $laporan->contains('id', $temuanCocok->id)
                && $laporan->contains('id', $temuanMenungguCocok->id)
                && ! $laporan->contains('id', $temuanTidakCocok->id))
            ->assertSee('Kunci Rani')
            ->assertSee('Kunci baru ditemukan')
            ->assertSee('Kunci ditemukan');
    }

    public function test_finder_can_confirm_claim_but_only_admin_can_approve_it(): void
    {
        $penemu = $this->buatPengguna('Zahira');
        $pengklaim = $this->buatPengguna('Rani');
        $admin = $this->buatPengguna('Admin');
        $admin->forceFill(['peran' => 'admin'])->save();
        $barang = $this->buatLaporanTemuan($penemu, $this->buatKategori('Dompet'), 'Dompet biru', 'tersedia');
        $klaim = $pengklaim->klaim()->create([
            'laporan_temuan_id' => $barang->id,
            'alasan_klaim' => 'Dompet saya hilang di kampus kemarin.',
            'ciri_khusus' => 'Ada kartu perpustakaan bernama Rani.',
        ]);

        $this->actingAs($pengklaim)
            ->patch(route('laporan-temuan.klaim.kepemilikan', [$barang, $klaim]))
            ->assertForbidden();

        $this->actingAs($penemu)
            ->patch(route('laporan-temuan.klaim.kepemilikan', [$barang, $klaim]))
            ->assertRedirect();

        $klaim->refresh();
        $this->assertNotNull($klaim->dikonfirmasi_penemu_pada);
        $this->assertSame('menunggu', $klaim->status);

        $this->actingAs($pengklaim)
            ->patch(route('admin.klaim.setujui', $klaim))
            ->assertRedirectToRoute('beranda');

        $this->assertDatabaseHas('klaim', ['id' => $klaim->id, 'status' => 'menunggu']);

        $this->actingAs($admin)
            ->patch(route('admin.klaim.setujui', $klaim))
            ->assertRedirect();

        $this->assertDatabaseHas('klaim', [
            'id' => $klaim->id,
            'status' => 'disetujui',
            'diproses_oleh' => $admin->id,
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'pengguna_id' => $penemu->id,
            'jenis' => 'klaim_disetujui',
        ]);
    }

    public function test_finder_confirmation_completes_handoff_and_closes_other_pending_claims(): void
    {
        $penemu = $this->buatPengguna('Zahira');
        $pemilik = $this->buatPengguna('Rani');
        $pengklaimLain = $this->buatPengguna('Dina');
        $admin = $this->buatPengguna('Admin');
        $admin->forceFill(['peran' => 'admin'])->save();
        $barang = $this->buatLaporanTemuan($penemu, $this->buatKategori('Dokumen'), 'Kartu mahasiswa', 'tersedia');
        $klaimDisetujui = $this->buatKlaim($pemilik, $barang);
        $klaimLain = $this->buatKlaim($pengklaimLain, $barang);

        $this->actingAs($penemu)
            ->patch(route('laporan-temuan.klaim.kepemilikan', [$barang, $klaimDisetujui]))
            ->assertRedirect();
        $this->actingAs($admin)
            ->patch(route('admin.klaim.setujui', $klaimDisetujui))
            ->assertRedirect();

        $this->actingAs($penemu)
            ->patch(route('laporan-temuan.klaim.penyerahan', [$barang, $klaimDisetujui]))
            ->assertRedirect();

        $klaimDisetujui->refresh();
        $this->assertNotNull($klaimDisetujui->diserahkan_penemu_pada);

        $this->actingAs($admin)
            ->patch(route('admin.klaim.pengembalian', $klaimDisetujui))
            ->assertRedirect();

        $this->assertDatabaseHas('laporan_temuan', ['id' => $barang->id, 'status' => 'dikembalikan']);
        $this->assertDatabaseHas('klaim', ['id' => $klaimDisetujui->id, 'status' => 'disetujui']);
        $this->assertDatabaseHas('klaim', ['id' => $klaimLain->id, 'status' => 'ditolak']);
        $this->assertDatabaseHas('notifikasi', [
            'pengguna_id' => $pengklaimLain->id,
            'jenis' => 'klaim_ditolak',
        ]);
    }

    public function test_submitting_a_claim_persists_it_and_notifies_the_finder(): void
    {
        $penemu = $this->buatPengguna('Zahira');
        $pengklaim = $this->buatPengguna('Rani');
        $barang = $this->buatLaporanTemuan($penemu, $this->buatKategori('Elektronik'), 'Headphone', 'tersedia');

        $this->actingAs($pengklaim)
            ->post(route('klaim.store', $barang), [
                'alasan_klaim' => 'Headphone ini milik saya dan hilang kemarin.',
                'ciri_khusus' => 'Ada stiker bintang biru pada kotak speaker.',
            ])
            ->assertRedirectToRoute('klaim.index');

        $this->assertDatabaseHas('klaim', [
            'laporan_temuan_id' => $barang->id,
            'pengguna_id' => $pengklaim->id,
            'status' => 'menunggu',
        ]);
        $this->assertDatabaseHas('notifikasi', [
            'pengguna_id' => $penemu->id,
            'jenis' => 'klaim_diajukan',
        ]);
    }

    public function test_admin_dashboard_counts_all_claims_and_pending_claims_from_database(): void
    {
        $admin = $this->buatPengguna('Admin');
        $admin->forceFill(['peran' => 'admin'])->save();
        $pemilik = $this->buatPengguna('Rani');
        $barang = $this->buatLaporanTemuan($this->buatPengguna('Zahira'), $this->buatKategori('Pakaian'), 'Jaket', 'tersedia');
        $klaimMenunggu = $this->buatKlaim($pemilik, $barang);
        $klaimDitolak = $this->buatKlaim($this->buatPengguna('Dina'), $barang, 'ditolak');

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertViewHas('jumlahKlaim', 2)
            ->assertViewHas('klaimBelumDiproses', 1)
            ->assertViewHas('jumlahTemuan', 1);

        $this->assertNotSame($klaimMenunggu->id, $klaimDitolak->id);
    }

    private function buatPengguna(string $nama): User
    {
        $pengguna = User::create([
            'nama' => $nama,
            'email' => strtolower($nama).'@example.test',
            'kata_sandi' => 'secret123',
        ]);

        $pengguna->forceFill(['aktif' => true, 'peran' => 'user'])->save();

        return $pengguna;
    }

    private function buatKategori(string $nama): Kategori
    {
        return Kategori::create(['nama' => $nama, 'slug' => strtolower(str_replace(' ', '-', $nama))]);
    }

    private function buatLaporanHilang(User $pemilik, Kategori $kategori, string $namaBarang): LaporanHilang
    {
        return $pemilik->laporanHilang()->create([
            'kategori_id' => $kategori->id,
            'nama_barang' => $namaBarang,
            'deskripsi' => 'Ciri barang untuk pengujian.',
            'lokasi_kehilangan' => 'Gedung A',
            'tanggal_kehilangan' => today(),
        ]);
    }

    private function buatLaporanTemuan(User $penemu, Kategori $kategori, string $namaBarang, string $status = 'menunggu'): LaporanTemuan
    {
        $laporan = $penemu->laporanTemuan()->create([
            'kategori_id' => $kategori->id,
            'nama_barang' => $namaBarang,
            'deskripsi' => 'Ciri barang untuk pengujian.',
            'lokasi_ditemukan' => 'Gedung B',
            'tanggal_ditemukan' => today(),
        ]);

        if ($status !== 'menunggu') {
            $laporan->forceFill(['status' => $status])->save();
        }

        return $laporan;
    }

    private function buatKlaim(User $pengklaim, LaporanTemuan $barang, string $status = 'menunggu'): Klaim
    {
        $klaim = $pengklaim->klaim()->create([
            'laporan_temuan_id' => $barang->id,
            'alasan_klaim' => 'Barang ini milik saya dan hilang kemarin.',
            'ciri_khusus' => 'Ada label nama saya di bagian dalam.',
        ]);

        if ($status !== 'menunggu') {
            $klaim->forceFill(['status' => $status])->save();
        }

        return $klaim;
    }
}
