@extends('layouts.user')
@section('judul', 'Detail Klaim')
@section('konten')
<x-judul-halaman judul="Detail Klaim Saya"><x-tombol varian="garis" :href="route('klaim.index')">Kembali</x-tombol></x-judul-halaman>
<div class="grid gap-6 lg:grid-cols-2"><x-kartu class="space-y-3">@if ($klaim->laporanTemuan->foto)<img src="{{ asset('storage/' . $klaim->laporanTemuan->foto) }}" alt="{{ $klaim->laporanTemuan->nama_barang }}" class="aspect-[4/3] w-full object-cover">@endif<h2 class="text-lg font-semibold">{{ $klaim->laporanTemuan->nama_barang }}</h2><p class="text-sm text-ink/60">{{ $klaim->laporanTemuan->kategori->nama }} · {{ $klaim->laporanTemuan->lokasi_ditemukan }}</p></x-kartu>
<x-kartu class="space-y-4">
	<div class="flex items-center justify-between"><h2 class="text-lg font-semibold">Data klaim</h2><x-badge-status :status="$klaim->status" /></div>
	<div><p class="text-sm font-semibold">Alasan klaim</p><p class="whitespace-pre-line text-sm">{{ $klaim->alasan_klaim }}</p></div>
	<div><p class="text-sm font-semibold">Ciri khusus</p><p class="whitespace-pre-line text-sm">{{ $klaim->ciri_khusus }}</p></div>
	@if ($klaim->foto_bukti)<img src="{{ asset('storage/' . $klaim->foto_bukti) }}" alt="Foto bukti klaim" class="max-h-64 object-contain">@endif
	<p class="text-xs text-ink/60">Diajukan {{ $klaim->created_at->translatedFormat('d F Y, H:i') }}</p>
	@if ($klaim->status === 'ditolak')
		<div class="bg-bahaya-muda p-4 text-sm text-bahaya"><strong>Alasan penolakan</strong><p>{{ $klaim->alasan_penolakan }}</p></div>
	@elseif ($klaim->status === 'disetujui')
		<div class="space-y-2 bg-sea/20 p-4 text-sm text-amalfi">
			<p>Klaim disetujui admin.</p>
			@if ($klaim->dikonfirmasi_penemu_pada)<p>Penemu mengonfirmasi kecocokan pada {{ $klaim->dikonfirmasi_penemu_pada->translatedFormat('d F Y, H:i') }}.</p>@endif
			@if ($klaim->diserahkan_penemu_pada && ! $klaim->dikembalikan_pada)<p>Penemu mencatat penyerahan. Menunggu konfirmasi akhir admin.</p>@endif
			@if ($klaim->dikembalikan_pada)<p>Barang diserahkan {{ $klaim->dikembalikan_pada->translatedFormat('d F Y, H:i') }}.</p>@endif
		</div>
	@endif
</x-kartu></div>
@endsection
