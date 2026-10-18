@props(['nama', 'label', 'tipe' => 'text', 'nilai' => null, 'wajib' => false, 'bantuan' => null, 'bag' => 'default'])
@php
    $adaGalat = $errors->getBag($bag)->has($nama);
    $kelasInput = 'block w-full border bg-white px-3 py-2.5 text-sm placeholder:text-ink/40 focus:outline-none focus:ring-2 focus:ring-sea ' . ($adaGalat ? 'border-bahaya' : 'border-ink/20 focus:border-amalfi');
@endphp
<div class="space-y-1.5">
    <label for="{{ $nama }}" class="block text-sm font-medium">{{ $label }}@if ($wajib)<span class="text-bahaya"> *</span>@endif</label>
    @if ($tipe === 'textarea')
        <textarea id="{{ $nama }}" name="{{ $nama }}" rows="4" @required($wajib) {{ $attributes->merge(['class' => $kelasInput]) }}>{{ old($nama, $nilai) }}</textarea>
    @elseif ($tipe === 'select')
        <select id="{{ $nama }}" name="{{ $nama }}" @required($wajib) {{ $attributes->merge(['class' => $kelasInput]) }}>{{ $slot }}</select>
    @elseif ($tipe === 'file')
        <input type="file" id="{{ $nama }}" name="{{ $nama }}" @required($wajib) {{ $attributes->merge(['class' => $kelasInput . ' file:mr-3 file:border-0 file:bg-sea/30 file:px-3 file:py-1.5 file:text-sm file:font-semibold file:text-amalfi']) }}>
    @else
        <input type="{{ $tipe }}" id="{{ $nama }}" name="{{ $nama }}" @if ($tipe !== 'password') value="{{ old($nama, $nilai) }}" @endif @required($wajib) {{ $attributes->merge(['class' => $kelasInput]) }}>
    @endif
    @if ($bantuan)<p class="text-xs text-ink/60">{{ $bantuan }}</p>@endif
    @error($nama, $bag)<p class="text-xs text-bahaya">{{ $message }}</p>@enderror
</div>
