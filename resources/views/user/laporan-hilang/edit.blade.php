@extends('layouts.user')
@section('judul', 'Edit Laporan Hilang')
@section('konten')<x-judul-halaman judul="Edit Laporan Barang Hilang" /><p class="mb-4 text-sm text-ink/60">Laporan yang ditolak akan masuk antrean verifikasi lagi setelah disimpan.</p><x-kartu class="max-w-2xl">@include('partials.form-laporan-hilang', ['laporan' => $laporan, 'daftarKategori' => $daftarKategori, 'aksi' => route('laporan-hilang.update', $laporan), 'metode' => 'PUT', 'batal' => route('laporan-hilang.index')])</x-kartu>@endsection
