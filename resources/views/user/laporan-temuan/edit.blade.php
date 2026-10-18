@extends('layouts.user')
@section('judul', 'Edit Laporan Temuan')
@section('konten')<x-judul-halaman judul="Edit Laporan Barang Temuan" /><p class="mb-4 text-sm text-ink/60">Laporan yang ditolak akan masuk antrean verifikasi lagi setelah disimpan.</p><x-kartu class="max-w-2xl">@include('partials.form-laporan-temuan', ['laporan' => $laporan, 'daftarKategori' => $daftarKategori, 'aksi' => route('laporan-temuan.update', $laporan), 'metode' => 'PUT', 'batal' => route('laporan-temuan.show', $laporan)])</x-kartu>@endsection
