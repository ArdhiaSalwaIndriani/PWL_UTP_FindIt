@extends('layouts.user')
@section('judul', 'Tambah Laporan Temuan')
@section('konten')<x-judul-halaman judul="Tambah Laporan Barang Temuan" /><x-kartu class="max-w-2xl">@include('partials.form-laporan-temuan', ['laporan' => null, 'daftarKategori' => $daftarKategori, 'aksi' => route('laporan-temuan.store'), 'metode' => 'POST', 'batal' => route('laporan-temuan.index')])</x-kartu>@endsection
