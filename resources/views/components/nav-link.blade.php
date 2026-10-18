@props(['href', 'aktif' => false])
<a href="{{ $href }}" {{ $attributes->class(['whitespace-nowrap px-3 py-2 text-sm font-medium transition', 'bg-white/20 text-white' => $aktif, 'text-white/80 hover:bg-white/10 hover:text-white' => ! $aktif]) }}>{{ $slot }}</a>
