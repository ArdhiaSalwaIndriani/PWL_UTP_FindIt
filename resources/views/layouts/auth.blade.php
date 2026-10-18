@extends('layouts.app')

@section('isi')
<div class="grid min-h-screen place-items-center bg-gelato/40 px-4 py-10">
    <div class="w-full max-w-md">
        <div class="mb-6 text-center">
            <a href="{{ route('masuk') }}" class="mx-auto grid size-12 place-items-center bg-citrus text-xl font-bold text-ink">F</a>
            <h1 class="mt-3 text-2xl font-bold text-amalfi">FindIt Campus</h1>
            <p class="text-sm text-ink/60">Temukan barangmu, bantu kembalikan barang orang lain.</p>
        </div>
        <x-flash />
        <x-kartu :padding="false" class="p-6 sm:p-8">@yield('konten')</x-kartu>
        @yield('bawah')
    </div>
</div>
@endsection
