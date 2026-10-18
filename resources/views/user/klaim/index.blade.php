@extends('layouts.user')
@section('judul', 'Klaim Saya')
@section('konten')
<x-judul-halaman judul="Klaim Saya"><x-tombol varian="aksen" :href="route('klaim.tersedia')">Lihat Barang Tersedia</x-tombol></x-judul-halaman>
<form method="GET" class="mb-5 flex gap-2"><select name="status" class="border border-ink/20 bg-white px-3 py-2 text-sm"><option value="">Semua status</option>@foreach (['menunggu', 'disetujui', 'ditolak', 'dibatalkan'] as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select><x-tombol type="submit" varian="lembut">Filter</x-tombol></form>
@if ($klaim->isEmpty())
	<x-kosong judul="Belum ada klaim">
		<x-tombol varian="aksen" :href="route('klaim.tersedia')">Jelajahi barang tersedia</x-tombol>
	</x-kosong>
@else
	<div class="space-y-3">
		@foreach ($klaim as $item)
			<x-kartu>
				<div class="flex flex-wrap items-center gap-4">
					<div class="min-w-0 flex-1">
						<p class="font-semibold">{{ $item->laporanTemuan->nama_barang }}</p>
						<p class="text-xs text-ink/60">{{ $item->created_at->translatedFormat('d M Y') }}</p>
					</div>
					<x-badge-status :status="$item->status" />
					<x-tombol ukuran="kecil" varian="lembut" :href="route('klaim.show', $item)">Lihat</x-tombol>
				</div>
			</x-kartu>
		@endforeach
	</div>
	<div class="mt-6">{{ $klaim->links() }}</div>
@endif
@endsection
