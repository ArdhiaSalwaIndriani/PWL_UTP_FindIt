<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaporanTemuanRequest extends FormRequest
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
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'deskripsi' => ['required', 'string'],
            'lokasi_ditemukan' => ['required', 'string', 'max:255'],
            'tanggal_ditemukan' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori barang wajib dipilih.',
            'kategori_id.exists' => 'Kategori yang dipilih tidak valid.',
            'nama_barang.required' => 'Nama barang wajib diisi.',
            'deskripsi.required' => 'Deskripsi barang wajib diisi.',
            'lokasi_ditemukan.required' => 'Lokasi ditemukan wajib diisi.',
            'tanggal_ditemukan.required' => 'Tanggal ditemukan wajib diisi.',
            'tanggal_ditemukan.before_or_equal' => 'Tanggal ditemukan tidak boleh melebihi hari ini.',
            'foto.image' => 'File harus berupa gambar.',
            'foto.mimes' => 'Foto harus berformat JPG, JPEG, PNG, atau WEBP.',
            'foto.max' => 'Ukuran foto maksimal 2 MB.',
        ];
    }
}
