@extends('layouts.admin')
@section('judul', 'Edit Laporan Hilang')
@section('konten')<x-judul-halaman judul="Edit Data Laporan Hilang" /><x-kartu class="max-w-2xl">@include('partials.form-laporan-hilang', ['laporan' => $laporan, 'daftarKategori' => $daftarKategori, 'aksi' => route('admin.laporan-hilang.update', $laporan), 'metode' => 'PUT', 'batal' => route('admin.laporan-hilang.show', $laporan)])</x-kartu>@endsection
