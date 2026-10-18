@extends('layouts.user')
@section('judul', 'Jelajah Barang Hilang')
@section('konten')
<x-judul-halaman judul="Semua Barang Hilang" sub="Laporan yang sudah disetujui admin" />
<x-kartu class="mb-5"><x-filter-cari :daftar-kategori="$daftarKategori" /></x-kartu>
@if ($laporan->isEmpty())<x-kosong judul="Tidak ada barang ditemukan" teks="Coba ubah kata kunci atau kategori." />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($laporan as $item)<x-kartu-barang :laporan="$item" jenis="hilang" />@endforeach</div><div class="mt-6">{{ $laporan->links() }}</div>@endif
@endsection
