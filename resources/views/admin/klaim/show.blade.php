@extends('layouts.admin')
@section('judul', 'Detail Klaim')
@section('konten')
@php $barang = $klaim->laporanTemuan; @endphp
<x-judul-halaman judul="Detail Klaim" :sub="'Diajukan ' . $klaim->created_at->translatedFormat('d F Y, H:i')"><x-tombol varian="garis" :href="route('admin.klaim.index')">Kembali</x-tombol></x-judul-halaman>
<div class="grid gap-6 lg:grid-cols-2">
	<x-kartu :padding="false" class="self-start overflow-hidden">
		<div class="aspect-[4/3] bg-gelato">
			@if ($barang->foto)
				<img src="{{ asset('storage/' . $barang->foto) }}" alt="{{ $barang->nama_barang }}" class="h-full w-full object-cover">
			@else
				<div class="grid h-full place-items-center text-sm text-ink/50">Tanpa foto</div>
			@endif
		</div>
		<div class="space-y-2 p-5">
			<div class="flex items-start justify-between gap-3"><h2 class="text-lg font-semibold">{{ $barang->nama_barang }}</h2><x-badge-status :status="$barang->status" /></div>
			<p class="text-sm text-ink/60">{{ $barang->kategori->nama }}</p>
			<p class="text-sm">{{ $barang->deskripsi }}</p>
			<p class="text-sm text-ink/60">Ditemukan di {{ $barang->lokasi_ditemukan }}, {{ $barang->tanggal_ditemukan->translatedFormat('d F Y') }}</p>
			<p class="text-sm text-ink/60">Penemu: {{ $barang->pelapor->nama }}</p>
		</div>
	</x-kartu>

	<div class="space-y-6">
		<x-kartu class="space-y-4">
			<div class="flex items-start justify-between gap-3"><div><h2 class="text-lg font-semibold">Pengklaim</h2><p class="text-sm">{{ $klaim->pengklaim->nama }}</p><p class="text-sm text-ink/60">{{ $klaim->pengklaim->email }}</p></div><x-badge-status :status="$klaim->status" /></div>
			<div><p class="text-sm font-semibold">Alasan klaim</p><p class="mt-1 whitespace-pre-line text-sm">{{ $klaim->alasan_klaim }}</p></div>
			<div><p class="text-sm font-semibold">Ciri khusus barang</p><p class="mt-1 whitespace-pre-line text-sm">{{ $klaim->ciri_khusus }}</p></div>
			<div>
				<p class="text-sm font-semibold">Foto bukti</p>
				@if ($klaim->foto_bukti)
					<a href="{{ asset('storage/' . $klaim->foto_bukti) }}" target="_blank" rel="noopener"><img src="{{ asset('storage/' . $klaim->foto_bukti) }}" alt="Foto bukti" class="mt-2 max-h-64 border object-contain"></a>
				@else
					<p class="mt-1 text-sm text-ink/60">Tidak ada bukti terlampir.</p>
				@endif
			</div>
			<div class="space-y-1 border-t pt-3 text-sm">
				<p>Konfirmasi penemu:
					@if ($klaim->dikonfirmasi_penemu_pada)
						{{ $klaim->dikonfirmasi_penemu_pada->translatedFormat('d F Y, H:i') }}
					@else
						Belum dikonfirmasi
					@endif
				</p>
				<p>Penyerahan oleh penemu:
					@if ($klaim->diserahkan_penemu_pada)
						{{ $klaim->diserahkan_penemu_pada->translatedFormat('d F Y, H:i') }}
					@else
						Belum dicatat
					@endif
				</p>
			</div>
		</x-kartu>

		<x-kartu class="space-y-4">
			<h2 class="text-lg font-semibold">Tindakan admin</h2>
			@if ($klaim->status === 'menunggu')
				<p class="text-sm text-ink/80">Periksa bukti kepemilikan. Konfirmasi penemu menjadi informasi pendukung.</p>
				<div class="flex flex-wrap items-start gap-3">
					<form method="POST" action="{{ route('admin.klaim.setujui', $klaim) }}">@csrf @method('PATCH')<x-tombol type="submit">Setujui klaim</x-tombol></form>
					<x-form-tolak :aksi="route('admin.klaim.tolak', $klaim)" />
				</div>
			@elseif ($klaim->status === 'disetujui' && ! $klaim->dikembalikan_pada)
				@if ($klaim->diserahkan_penemu_pada)
					<p class="text-sm text-ink/80">Penemu mencatat barang sudah diserahkan. Konfirmasi penyelesaian klaim.</p>
					<form method="POST" action="{{ route('admin.klaim.pengembalian', $klaim) }}" onsubmit="return confirm('Konfirmasi barang sudah diserahkan kepada pemilik?')">@csrf @method('PATCH')<x-tombol type="submit" varian="aksen">Konfirmasi pengembalian</x-tombol></form>
				@else
					<p class="text-sm text-ink/60">Menunggu penemu mencatat penyerahan barang.</p>
				@endif
			@elseif ($klaim->dikembalikan_pada)
				<p class="text-sm">Barang dikembalikan {{ $klaim->dikembalikan_pada->translatedFormat('d F Y, H:i') }}.</p>
			@elseif ($klaim->status === 'ditolak')
				<div class="bg-bahaya-muda px-4 py-3 text-sm text-bahaya"><p class="font-semibold">Alasan penolakan</p><p>{{ $klaim->alasan_penolakan }}</p></div>
			@else
				<p class="text-sm text-ink/60">Klaim ini dibatalkan.</p>
			@endif
			@if ($klaim->pemroses)
				<p class="border-t pt-3 text-xs text-ink/60">Diproses oleh {{ $klaim->pemroses->nama }} pada {{ $klaim->diproses_pada->translatedFormat('d F Y, H:i') }}</p>
			@endif
		</x-kartu>
	</div>
</div>
@endsection
