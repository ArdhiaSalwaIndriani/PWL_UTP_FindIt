<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanHilangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'kategori_id' => ['required', 'exists:kategori,id'],
            'nama_barang' => ['required', 'string', 'max:255'],
            'deskripsi' => ['required', 'string'],
            'lokasi_kehilangan' => ['required', 'string', 'max:255'],
            'tanggal_kehilangan' => ['required', 'date', 'before_or_equal:today'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori barang wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'deskripsi.required' => 'Deskripsi barang wajib diisi.',
            'lokasi_kehilangan.required' => 'Lokasi kehilangan wajib diisi.',
            'tanggal_kehilangan.required' => 'Tanggal kehilangan wajib diisi.',
            'tanggal_kehilangan.before_or_equal' => 'Tanggal kehilangan tidak boleh melebihi hari ini.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
