@extends('layouts.app')

@section('isi')
@php
    $menu = [
        ['Beranda', route('beranda'), request()->routeIs('beranda', 'jelajah.*')],
        ['Barang Hilang', route('laporan-hilang.index'), request()->routeIs('laporan-hilang.*')],
        ['Barang Temuan', route('laporan-temuan.index'), request()->routeIs('laporan-temuan.*')],
        ['Klaim Saya', route('klaim.index'), request()->routeIs('klaim.*')],
        ['Notifikasi', route('notifikasi.index'), request()->routeIs('notifikasi.*')],
        ['Profil', route('profil'), request()->routeIs('profil*')],
    ];
    $jumlahNotifikasi = auth()->user()->notifikasi()->belumDibaca()->count();
@endphp
<header class="bg-amalfi text-white">
    <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-4 py-3">
        <a href="{{ route('beranda') }}" class="flex items-center gap-2 text-lg font-bold"><span class="grid size-8 place-items-center bg-citrus text-ink">F</span>FindIt Campus</a>
        <div class="flex items-center gap-3 text-sm"><span class="hidden sm:inline">{{ auth()->user()->nama }}</span><form method="POST" action="{{ route('keluar') }}">@csrf<x-tombol type="submit" varian="aksen" ukuran="kecil">Keluar</x-tombol></form></div>
    </div>
    <nav class="mx-auto flex max-w-6xl gap-1 overflow-x-auto px-4 pb-2">
        @foreach ($menu as [$label, $url, $aktif])
            <x-nav-link :href="$url" :aktif="$aktif">{{ $label }}@if ($label === 'Notifikasi' && $jumlahNotifikasi > 0)<span class="ml-1 bg-citrus px-1.5 py-0.5 text-xs font-semibold text-ink">{{ $jumlahNotifikasi }}</span>@endif</x-nav-link>
        @endforeach
    </nav>
</header>
<main class="mx-auto max-w-6xl px-4 py-8"><x-flash />@yield('konten')</main>
@endsection
