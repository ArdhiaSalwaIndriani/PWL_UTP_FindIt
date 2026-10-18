@extends('layouts.user')
@section('judul', 'Detail Laporan Hilang')
@section('konten')
<x-judul-halaman judul="Detail Laporan Barang Hilang"><x-tombol varian="garis" :href="route('laporan-hilang.index')">Kembali</x-tombol></x-judul-halaman>
<div class="grid gap-6 lg:grid-cols-3">
	<x-kartu class="space-y-4 lg:col-span-2">
		@if ($laporan->foto)
			<img src="{{ asset('storage/' . $laporan->foto) }}" alt="{{ $laporan->nama_barang }}" class="max-h-96 w-full object-contain bg-gelato">
		@endif
		<div class="flex flex-wrap items-center justify-between gap-3">
			<h1 class="text-2xl font-semibold">{{ $laporan->nama_barang }}</h1>
			<x-badge-status :status="$laporan->status" />
		</div>
		<p class="text-sm text-ink/60">{{ $laporan->kategori->nama }}</p>
		<p class="whitespace-pre-line text-sm">{{ $laporan->deskripsi }}</p>
		<p class="text-sm">{{ $laporan->lokasi_kehilangan }} · {{ $laporan->tanggal_kehilangan->translatedFormat('d F Y') }}</p>
		@if ($laporan->status === 'ditolak')
			<div class="bg-bahaya-muda p-4 text-sm text-bahaya">
				<strong>Alasan penolakan</strong>
				<p>{{ $laporan->alasan_penolakan }}</p>
			</div>
		@endif
		@if ($laporan->diverifikasi_pada)
			<p class="text-xs text-ink/60">Diverifikasi {{ $laporan->diverifikasi_pada->translatedFormat('d F Y') }}</p>
		@endif
		@if ($laporan->bisaDiubahPemilik())
			<div class="flex gap-2 border-t pt-4">
				<x-tombol varian="garis" :href="route('laporan-hilang.edit', $laporan)">Edit</x-tombol>
				<form method="POST" action="{{ route('laporan-hilang.destroy', $laporan) }}" onsubmit="return confirm('Hapus laporan ini?')">
					@csrf
					@method('DELETE')
					<x-tombol type="submit" varian="bahaya">Hapus</x-tombol>
				</form>
			</div>
		@endif
	</x-kartu>
	<x-kartu class="self-start">
		<h2 class="font-semibold">Informasi pelapor</h2>
		<p class="mt-2 text-sm">{{ $laporan->pelapor->nama }}</p>
		<p class="text-xs text-ink/60">Dibuat {{ $laporan->created_at->translatedFormat('d F Y') }}</p>
	</x-kartu>
</div>
@endsection
