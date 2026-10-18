@props(['laporan', 'jenis'])
@php
    $hilang = $jenis === 'hilang';
    $lokasi = $hilang ? $laporan->lokasi_kehilangan : $laporan->lokasi_ditemukan;
    $tanggal = $hilang ? $laporan->tanggal_kehilangan : $laporan->tanggal_ditemukan;
    $tautan = $hilang ? route('jelajah.hilang.detail', $laporan) : route('jelajah.temuan.detail', $laporan);
@endphp
<a href="{{ $tautan }}" class="group block overflow-hidden border border-ink/10 bg-white shadow-sm transition hover:-translate-y-0.5 hover:shadow-md">
    <div class="aspect-[4/3] bg-gelato">@if ($laporan->foto)<img src="{{ asset('storage/' . $laporan->foto) }}" alt="{{ $laporan->nama_barang }}" class="h-full w-full object-cover" loading="lazy">@else<div class="grid h-full place-items-center text-sm text-ink/50">Tanpa foto</div>@endif</div>
    <div class="space-y-2 p-4"><div class="flex items-start justify-between gap-2"><h3 class="line-clamp-1 font-semibold leading-snug">{{ $laporan->nama_barang }}</h3><div class="flex shrink-0 flex-wrap justify-end gap-1"><span class="px-2 py-0.5 text-xs font-semibold {{ $hilang ? 'bg-citrus text-ink' : 'bg-amalfi text-white' }}">{{ $hilang ? 'Hilang' : 'Temuan' }}</span><x-badge-status :status="$laporan->status" /></div></div><p class="text-xs text-ink/60">{{ $laporan->kategori->nama }}</p><p class="line-clamp-2 text-sm text-ink/80">{{ $laporan->deskripsi }}</p><p class="text-xs text-ink/60">{{ $lokasi }} &middot; {{ $tanggal->translatedFormat('d M Y') }}</p></div>
</a>
