@props(['varian' => 'utama', 'ukuran' => 'sedang', 'href' => null, 'type' => 'button'])
@php
    $warna = match ($varian) {
        'aksen' => 'bg-citrus text-ink hover:bg-citrus-dark',
        'garis' => 'border border-amalfi text-amalfi hover:bg-amalfi/10',
        'lembut' => 'bg-sea/30 text-amalfi hover:bg-sea/50',
        'terang' => 'bg-white text-amalfi hover:bg-gelato',
        'bahaya' => 'bg-bahaya text-white hover:bg-bahaya/90',
        default => 'bg-amalfi text-white hover:bg-amalfi-dark',
    };
    $ukur = $ukuran === 'kecil' ? 'px-3 py-1.5 text-xs' : 'px-4 py-2.5 text-sm';
    $kelas = "inline-flex items-center justify-center gap-2 font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-sea focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 {$ukur} {$warna}";
@endphp
@if ($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</a>
@else
    <button type="{{ $type }}" {{ $attributes->merge(['class' => $kelas]) }}>{{ $slot }}</button>
@endif
