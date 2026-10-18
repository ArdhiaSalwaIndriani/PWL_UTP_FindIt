@props(['status'])
@php
    $peta = [
        'menunggu' => ['Menunggu', 'bg-gelato text-ink'],
        'disetujui' => ['Disetujui', 'bg-sea/30 text-amalfi'],
        'tersedia' => ['Tersedia', 'bg-sea/30 text-amalfi'],
        'ditolak' => ['Ditolak', 'bg-bahaya-muda text-bahaya'],
        'dibatalkan' => ['Dibatalkan', 'bg-ink/10 text-ink/70'],
        'selesai' => ['Selesai', 'bg-amalfi text-white'],
        'dikembalikan' => ['Dikembalikan', 'bg-amalfi text-white'],
    ];
    [$label, $kelas] = $peta[$status] ?? [ucfirst($status), 'bg-ink/10 text-ink'];
@endphp
<span {{ $attributes->class(['inline-flex items-center px-2.5 py-1 text-xs font-semibold', $kelas]) }}>{{ $label }}</span>
