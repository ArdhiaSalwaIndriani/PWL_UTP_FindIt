@extends('layouts.user')
@section('judul', 'Tambah Laporan Hilang')
@section('konten')<x-judul-halaman judul="Tambah Laporan Barang Hilang" /><x-kartu class="max-w-2xl">@include('partials.form-laporan-hilang', ['laporan' => null, 'daftarKategori' => $daftarKategori, 'aksi' => route('laporan-hilang.store'), 'metode' => 'POST', 'batal' => route('laporan-hilang.index')])</x-kartu>@endsection
