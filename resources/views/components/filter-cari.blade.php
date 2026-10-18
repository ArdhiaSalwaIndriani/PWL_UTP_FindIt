@props(['daftarKategori' => [], 'opsiStatus' => [], 'placeholder' => 'Cari nama barang...'])
@php $kelasKontrol = 'border border-ink/20 bg-white px-3 py-2.5 text-sm focus:border-amalfi focus:outline-none focus:ring-2 focus:ring-sea'; @endphp
<form method="GET" {{ $attributes->class(['flex flex-col gap-3 sm:flex-row sm:items-center']) }}>
    <input type="search" name="q" value="{{ request('q') }}" placeholder="{{ $placeholder }}" aria-label="Cari" class="min-w-0 flex-1 placeholder:text-ink/40 {{ $kelasKontrol }}">
    @if (count($daftarKategori))<select name="kategori" aria-label="Kategori" class="{{ $kelasKontrol }}"><option value="">Semua kategori</option>@foreach ($daftarKategori as $kategori)<option value="{{ $kategori->id }}" @selected(request('kategori') == $kategori->id)>{{ $kategori->nama }}</option>@endforeach</select>@endif
    @if (count($opsiStatus))<select name="status" aria-label="Status" class="{{ $kelasKontrol }}"><option value="">Semua status</option>@foreach ($opsiStatus as $status)<option value="{{ $status }}" @selected(request('status') === $status)>{{ ucfirst($status) }}</option>@endforeach</select>@endif
    <div class="flex gap-2"><x-tombol type="submit">Cari</x-tombol>@if (request()->anyFilled(['q', 'kategori', 'status']))<x-tombol varian="garis" :href="url()->current()">Reset</x-tombol>@endif</div>
</form>
