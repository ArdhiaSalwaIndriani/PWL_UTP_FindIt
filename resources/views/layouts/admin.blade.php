@extends('layouts.app')

@section('isi')
@php
    $menu = [
        ['Dashboard', route('admin.dashboard'), request()->routeIs('admin.dashboard')],
        ['Laporan Hilang', route('admin.laporan-hilang.index'), request()->routeIs('admin.laporan-hilang.*')],
        ['Barang Temuan', route('admin.laporan-temuan.index'), request()->routeIs('admin.laporan-temuan.*')],
        ['Klaim Barang', route('admin.klaim.index'), request()->routeIs('admin.klaim.*')],
        ['Kategori', route('admin.kategori.index'), request()->routeIs('admin.kategori.*')],
        ['Pengguna', route('admin.pengguna.index'), request()->routeIs('admin.pengguna.*')],
        ['Profil', route('admin.profil'), request()->routeIs('admin.profil*')],
    ];
    $klaimMenunggu = \App\Models\Klaim::where('status', 'menunggu')->count();
@endphp
<div class="md:flex">
    <aside class="bg-ink text-white md:sticky md:top-0 md:flex md:h-screen md:w-64 md:shrink-0 md:flex-col md:overflow-y-auto">
        <div class="flex items-center justify-between px-4 py-3 md:block md:px-5 md:py-6"><a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 text-lg font-bold"><span class="grid size-8 place-items-center bg-citrus text-ink">F</span>FindIt <span class="text-citrus">Admin</span></a><form method="POST" action="{{ route('keluar') }}" class="md:hidden">@csrf<x-tombol type="submit" varian="aksen" ukuran="kecil">Keluar</x-tombol></form></div>
        <nav class="flex gap-1 overflow-x-auto px-3 pb-3 md:flex-1 md:flex-col md:overflow-visible">
            @foreach ($menu as [$label, $url, $aktif])
                <a href="{{ $url }}" class="flex items-center justify-between whitespace-nowrap px-3 py-2 text-sm font-medium transition {{ $aktif ? 'bg-citrus text-ink' : 'text-white/80 hover:bg-white/10' }}">{{ $label }}@if ($label === 'Klaim Barang' && $klaimMenunggu > 0)<span class="ml-2 bg-white px-1.5 py-0.5 text-xs font-semibold text-ink">{{ $klaimMenunggu }}</span>@endif</a>
            @endforeach
        </nav>
        <div class="hidden border-t border-white/10 p-4 md:block"><p class="truncate text-sm font-medium">{{ auth()->user()->nama }}</p><p class="mb-3 truncate text-xs text-white/60">{{ auth()->user()->email }}</p><form method="POST" action="{{ route('keluar') }}">@csrf<x-tombol type="submit" varian="aksen" ukuran="kecil" class="w-full">Keluar</x-tombol></form></div>
    </aside>
    <main class="min-w-0 flex-1 px-4 py-6 md:px-8 md:py-8"><x-flash />@yield('konten')</main>
</div>
@endsection
