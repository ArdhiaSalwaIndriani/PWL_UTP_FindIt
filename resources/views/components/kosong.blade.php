@props(['judul' => 'Belum ada data', 'teks' => null])
<div class="border border-dashed border-ink/20 bg-white/60 px-6 py-12 text-center"><p class="font-semibold">{{ $judul }}</p>@if ($teks)<p class="mt-1 text-sm text-ink/60">{{ $teks }}</p>@endif @if (! $slot->isEmpty())<div class="mt-4">{{ $slot }}</div>@endif</div>
