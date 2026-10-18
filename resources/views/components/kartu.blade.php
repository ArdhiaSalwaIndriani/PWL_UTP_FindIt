@props(['padding' => true])
<div {{ $attributes->class(['border border-ink/10 bg-white shadow-sm', 'p-5' => $padding]) }}>{{ $slot }}</div>
