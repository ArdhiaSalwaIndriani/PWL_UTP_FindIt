@extends('layouts.user')
@section('judul', 'Beranda')
@section('konten')
<section class="bg-amalfi p-6 text-white sm:p-8"><p class="mb-3 text-xs font-semibold uppercase text-citrus">FindIt Campus</p><h1 class="max-w-2xl text-2xl font-semibold sm:text-3xl">Kehilangan atau menemukan barang?</h1><p class="mt-2 max-w-xl text-sm text-white/80">Temukan laporan terbaru atau bantu barang yang ditemukan kembali ke pemiliknya.</p><div class="mt-5 flex flex-wrap gap-3"><x-tombol :href="route('laporan-hilang.create')" varian="aksen">Lapor Barang Hilang</x-tombol><x-tombol :href="route('laporan-temuan.create')" varian="terang">Lapor Barang Temuan</x-tombol></div></section>
<x-kartu class="mt-6"><x-filter-cari :daftar-kategori="$daftarKategori" /></x-kartu>
<section class="mt-8 grid gap-6 lg:grid-cols-2">
	<x-kartu :padding="false">
		<div class="flex items-center justify-between border-b px-5 py-4">
			<h2 class="font-semibold">Laporan Hilang Saya</h2>
			<a href="{{ route('laporan-hilang.index') }}" class="text-sm font-semibold text-amalfi hover:underline">Semua</a>
		</div>
		@forelse ($laporanHilangSaya as $laporan)
			<a href="{{ route('laporan-hilang.show', $laporan) }}" class="flex items-center justify-between gap-3 border-b px-5 py-3 hover:bg-gelato/30">
				<div class="min-w-0"><p class="truncate font-medium">{{ $laporan->nama_barang }}</p><p class="text-xs text-ink/60">{{ $laporan->kategori->nama }} · {{ $laporan->tanggal_kehilangan->translatedFormat('d M Y') }}</p></div>
				<x-badge-status :status="$laporan->status" />
			</a>
		@empty
			<p class="px-5 py-6 text-sm text-ink/60">Kamu belum membuat laporan barang hilang.</p>
		@endforelse
	</x-kartu>
	<x-kartu :padding="false">
		<div class="flex items-center justify-between border-b px-5 py-4">
			<h2 class="font-semibold">Laporan Temuan Saya</h2>
			<a href="{{ route('laporan-temuan.index') }}" class="text-sm font-semibold text-amalfi hover:underline">Semua</a>
		</div>
		@forelse ($laporanTemuanSaya as $laporan)
			<a href="{{ route('laporan-temuan.show', $laporan) }}" class="flex items-center justify-between gap-3 border-b px-5 py-3 hover:bg-gelato/30">
				<div class="min-w-0"><p class="truncate font-medium">{{ $laporan->nama_barang }}</p><p class="text-xs text-ink/60">{{ $laporan->kategori->nama }} · {{ $laporan->tanggal_ditemukan->translatedFormat('d M Y') }}</p></div>
				<x-badge-status :status="$laporan->status" />
			</a>
		@empty
			<p class="px-5 py-6 text-sm text-ink/60">Kamu belum membuat laporan barang temuan.</p>
		@endforelse
	</x-kartu>
</section>
<section class="mt-10">
	<x-judul-halaman tag="h2" judul="Temuan yang Mungkin Cocok" sub="Barang tersedia dari pengguna lain dengan kategori laporan hilangmu" />
	@if ($temuanRelevan->isEmpty())
		<x-kosong judul="Belum ada temuan yang cocok" teks="Temuan baru akan muncul setelah diverifikasi admin." />
	@else
		<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">
			@foreach ($temuanRelevan as $laporan)
				<x-kartu-barang :laporan="$laporan" jenis="temuan" />
			@endforeach
		</div>
	@endif
</section>
<section class="mt-10"><x-judul-halaman tag="h2" judul="Barang Hilang Terbaru"><a href="{{ route('jelajah.hilang') }}" class="text-sm font-semibold text-amalfi hover:underline">Lihat semua</a></x-judul-halaman>@if ($hilangTerbaru->isEmpty())<x-kosong judul="Belum ada laporan barang hilang" />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($hilangTerbaru as $laporan)<x-kartu-barang :laporan="$laporan" jenis="hilang" />@endforeach</div>@endif</section>
<section class="mt-10"><x-judul-halaman tag="h2" judul="Barang Temuan Terbaru"><a href="{{ route('jelajah.temuan') }}" class="text-sm font-semibold text-amalfi hover:underline">Lihat semua</a></x-judul-halaman>@if ($temuanTerbaru->isEmpty())<x-kosong judul="Belum ada laporan barang temuan" />@else<div class="grid gap-5 sm:grid-cols-2 lg:grid-cols-4">@foreach ($temuanTerbaru as $laporan)<x-kartu-barang :laporan="$laporan" jenis="temuan" />@endforeach</div>@endif</section>
@endsection
