@extends('layouts.user')
@section('judul', 'Detail Laporan Temuan')
@section('konten')
<x-judul-halaman judul="Detail Laporan Barang Temuan"><x-tombol varian="garis" :href="route('laporan-temuan.index')">Kembali</x-tombol></x-judul-halaman>
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
		<p class="text-sm">{{ $laporan->lokasi_ditemukan }} · {{ $laporan->tanggal_ditemukan->translatedFormat('d F Y') }}</p>
		@if ($laporan->status === 'ditolak')
			<div class="bg-bahaya-muda p-4 text-sm text-bahaya">
				<strong>Alasan penolakan</strong>
				<p>{{ $laporan->alasan_penolakan }}</p>
			</div>
		@endif
		@if ($laporan->diverifikasi_pada)
			<p class="text-xs text-ink/60">Diverifikasi {{ $laporan->diverifikasi_pada->translatedFormat('d F Y') }}</p>
		@endif
		@if ($laporan->status === 'dikembalikan' && $laporan->dikembalikan_pada)
			<p class="text-xs text-ink/60">Dikembalikan {{ $laporan->dikembalikan_pada->translatedFormat('d F Y') }}</p>
		@endif
		@if ($laporan->bisaDiubahPemilik())
			<div class="flex gap-2 border-t pt-4">
				<x-tombol varian="garis" :href="route('laporan-temuan.edit', $laporan)">Edit</x-tombol>
				<form method="POST" action="{{ route('laporan-temuan.destroy', $laporan) }}" onsubmit="return confirm('Hapus laporan ini?')">
					@csrf
					@method('DELETE')
					<x-tombol type="submit" varian="bahaya">Hapus</x-tombol>
				</form>
			</div>
		@endif
	</x-kartu>
	<x-kartu class="self-start">
		<h2 class="font-semibold">Informasi penemu</h2>
		<p class="mt-2 text-sm">{{ $laporan->pelapor->nama }}</p>
		<p class="text-xs text-ink/60">Dibuat {{ $laporan->created_at->translatedFormat('d F Y') }}</p>
	</x-kartu>
</div>
@if ($laporan->pengguna_id === auth()->id())
	<section class="mt-8">
		<x-judul-halaman tag="h2" judul="Klaim untuk barang ini" sub="Penemu dapat membantu mencocokkan ciri dan mencatat penyerahan. Admin tetap memutuskan klaim." />
		@if ($laporan->klaim->isEmpty())
			<x-kosong judul="Belum ada klaim untuk barang ini" />
		@else
			<div class="space-y-4">
				@foreach ($laporan->klaim as $klaim)
					<x-kartu class="space-y-4">
						<div class="flex flex-wrap items-center justify-between gap-3">
							<div><p class="font-semibold">{{ $klaim->pengklaim->nama }}</p><p class="text-xs text-ink/60">Diajukan {{ $klaim->created_at->translatedFormat('d F Y') }}</p></div>
							<x-badge-status :status="$klaim->status" />
						</div>
						<div><p class="text-sm font-semibold">Alasan klaim</p><p class="whitespace-pre-line text-sm">{{ $klaim->alasan_klaim }}</p></div>
						<div><p class="text-sm font-semibold">Ciri khusus</p><p class="whitespace-pre-line text-sm">{{ $klaim->ciri_khusus }}</p></div>
						@if ($klaim->dikonfirmasi_penemu_pada)
							<p class="text-xs text-amalfi">Kecocokan dikonfirmasi {{ $klaim->dikonfirmasi_penemu_pada->translatedFormat('d F Y, H:i') }}. Admin tetap memeriksa bukti.</p>
						@endif
						@if ($klaim->diserahkan_penemu_pada)
							<p class="text-xs text-amalfi">Penyerahan dicatat {{ $klaim->diserahkan_penemu_pada->translatedFormat('d F Y, H:i') }}. Menunggu konfirmasi admin.</p>
						@endif
						@if (in_array($klaim->status, ['menunggu', 'disetujui']) && ! $klaim->dikonfirmasi_penemu_pada)
							<form method="POST" action="{{ route('laporan-temuan.klaim.kepemilikan', [$laporan, $klaim]) }}">@csrf @method('PATCH')<x-tombol type="submit" varian="lembut">Konfirmasi ciri cocok</x-tombol></form>
						@elseif ($klaim->status === 'disetujui' && ! $klaim->diserahkan_penemu_pada)
							<form method="POST" action="{{ route('laporan-temuan.klaim.penyerahan', [$laporan, $klaim]) }}" onsubmit="return confirm('Catat bahwa barang sudah diserahkan kepada pengklaim?')">@csrf @method('PATCH')<x-tombol type="submit" varian="aksen">Catat penyerahan barang</x-tombol></form>
						@endif
					</x-kartu>
				@endforeach
			</div>
		@endif
	</section>
@endif
@endsection
