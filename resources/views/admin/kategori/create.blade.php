@extends('layouts.admin')
@section('judul', 'Tambah Kategori')
@section('konten')<x-judul-halaman judul="Tambah Kategori" /><x-kartu class="max-w-md"><form method="POST" action="{{ route('admin.kategori.store') }}" class="space-y-5">@csrf<x-field nama="nama" label="Nama kategori" wajib /><div class="flex gap-3"><x-tombol type="submit" varian="aksen">Simpan</x-tombol><x-tombol varian="garis" :href="route('admin.kategori.index')">Batal</x-tombol></div></form></x-kartu>@endsection
