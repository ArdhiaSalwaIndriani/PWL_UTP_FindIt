@php $l = $laporan ?? null; @endphp
<form method="POST" action="{{ $aksi }}" enctype="multipart/form-data" class="space-y-5">@csrf @if (($metode ?? 'POST') !== 'POST')@method($metode)@endif
<x-field nama="nama_barang" label="Nama barang" :nilai="$l?->nama_barang" wajib />
<x-field nama="kategori_id" label="Kategori" tipe="select" wajib><option value="">Pilih kategori</option>@foreach ($daftarKategori as $kategori)<option value="{{ $kategori->id }}" @selected(old('kategori_id', $l?->kategori_id) == $kategori->id)>{{ $kategori->nama }}</option>@endforeach</x-field>
<x-field nama="deskripsi" label="Deskripsi / ciri-ciri" tipe="textarea" :nilai="$l?->deskripsi" bantuan="Tulis warna, merek, ciri khusus, atau isi barang." wajib />
<div class="grid gap-5 sm:grid-cols-2"><x-field nama="lokasi_kehilangan" label="Lokasi kehilangan" :nilai="$l?->lokasi_kehilangan" wajib /><x-field nama="tanggal_kehilangan" label="Tanggal kehilangan" tipe="date" :nilai="$l?->tanggal_kehilangan?->format('Y-m-d')" :max="now()->toDateString()" wajib /></div>
<div>@if ($l?->foto)<div class="mb-3 flex items-center gap-3"><img src="{{ asset('storage/' . $l->foto) }}" alt="Foto saat ini" class="size-20 object-cover"><p class="text-xs text-ink/60">Pilih file baru untuk mengganti foto.</p></div>@endif<x-field nama="foto" label="Foto barang" tipe="file" accept="image/*" bantuan="JPG, PNG, atau WEBP. Maksimal 2 MB." /></div>
<div class="flex flex-wrap gap-3"><x-tombol type="submit" varian="aksen">{{ $l ? 'Simpan perubahan' : 'Kirim laporan' }}</x-tombol></div></form>
<div class="mt-3"><x-tombol varian="garis" :href="$batal">Batal perubahan</x-tombol></div>
