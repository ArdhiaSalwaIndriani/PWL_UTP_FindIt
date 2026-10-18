@extends('layouts.user')
@section('judul', 'Jelajah Barang Temuan')
@section('konten')
<x-judul-halaman judul="Barang Temuan Pengguna Lain" sub="Barang yang tersedia untuk diklaim" />
<x-kartu class="mb-5"><x-filter-cari :daftar-kategori="$daftarKategori" /></x-kartu>
@if ($laporan->isEmpty())<x-kosong judul="Tidak ada barang ditemukan" teks="Coba ubah kata kunci atau kategori." />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($laporan as $item)<x-kartu-barang :laporan="$item" jenis="temuan" />@endforeach</div><div class="mt-6">{{ $laporan->links() }}</div>@endif
@endsection
