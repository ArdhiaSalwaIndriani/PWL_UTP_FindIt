@extends('layouts.auth')
@section('judul', 'Masuk')
@section('konten')
<h2 class="mb-1 text-xl font-semibold">Masuk</h2><p class="mb-6 text-sm text-ink/60">Masuk untuk melaporkan atau mencari barang.</p>
<form method="POST" action="{{ route('masuk') }}" class="space-y-4">@csrf<x-field nama="email" label="Email" tipe="email" wajib autofocus autocomplete="email" /><x-field nama="kata_sandi" label="Kata sandi" tipe="password" wajib autocomplete="current-password" /><label class="flex items-center gap-2 text-sm"><input type="checkbox" name="ingat_saya" value="1" class="size-4 accent-amalfi">Ingat saya</label><x-tombol type="submit" class="w-full">Masuk</x-tombol></form>
@endsection
@section('bawah')<p class="mt-4 text-center text-sm">Belum punya akun? <a href="{{ route('daftar') }}" class="font-semibold text-amalfi hover:underline">Daftar</a></p>@endsection
