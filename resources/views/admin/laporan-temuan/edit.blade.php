@extends('layouts.admin')
@section('judul', 'Edit Laporan Temuan')
@section('konten')<x-judul-halaman judul="Edit Data Laporan Temuan" /><x-kartu class="max-w-2xl">@include('partials.form-laporan-temuan', ['laporan' => $laporan, 'daftarKategori' => $daftarKategori, 'aksi' => route('admin.laporan-temuan.update', $laporan), 'metode' => 'PUT', 'batal' => route('admin.laporan-temuan.show', $laporan)])</x-kartu>@endsection
