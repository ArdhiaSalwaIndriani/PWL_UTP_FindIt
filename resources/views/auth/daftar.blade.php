@extends('layouts.auth')
@section('judul', 'Daftar')
@section('konten')
<h2 class="mb-1 text-xl font-semibold">Buat akun</h2><p class="mb-6 text-sm text-ink/60">Satu akun untuk membantu barang kembali ke pemiliknya.</p>
<form method="POST" action="{{ route('daftar') }}" class="space-y-4">@csrf<x-field nama="nama" label="Nama lengkap" wajib /><x-field nama="email" label="Email" tipe="email" wajib autocomplete="email" /><x-field nama="kata_sandi" label="Kata sandi" tipe="password" bantuan="Minimal 8 karakter." wajib autocomplete="new-password" /><x-field nama="kata_sandi_confirmation" label="Ulangi kata sandi" tipe="password" wajib autocomplete="new-password" /><x-tombol type="submit" class="w-full">Daftar</x-tombol></form>
@endsection
@section('bawah')<p class="mt-4 text-center text-sm">Sudah punya akun? <a href="{{ route('masuk') }}" class="font-semibold text-amalfi hover:underline">Masuk</a></p>@endsection
