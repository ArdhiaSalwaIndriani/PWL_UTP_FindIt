@props(['judul', 'sub' => null, 'tag' => 'h1'])
<div class="mb-5 flex flex-wrap items-end justify-between gap-3">
    <div><{{ $tag }} class="{{ $tag === 'h1' ? 'text-2xl' : 'text-xl' }} font-semibold">{{ $judul }}</{{ $tag }}>@if ($sub)<p class="text-sm text-ink/60">{{ $sub }}</p>@endif</div>
    <div class="flex items-center gap-2">{{ $slot }}</div>
</div>
