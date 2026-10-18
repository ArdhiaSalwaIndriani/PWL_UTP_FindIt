@extends('layouts.admin')
@section('judul', 'Edit Kategori')
@section('konten')<x-judul-halaman judul="Edit Kategori" /><x-kartu class="max-w-md"><form method="POST" action="{{ route('admin.kategori.update', $kategori) }}" class="space-y-5">@csrf @method('PUT')<x-field nama="nama" label="Nama kategori" :nilai="$kategori->nama" wajib /><div class="flex gap-3"><x-tombol type="submit" varian="aksen">Simpan perubahan</x-tombol><x-tombol varian="garis" :href="route('admin.kategori.index')">Batal</x-tombol></div></form></x-kartu>@endsection
