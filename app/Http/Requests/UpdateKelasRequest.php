<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKelasRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nama' => 'required|string|max:255',
            'thumbnail' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kelas wajib diisi.',
            'nama.string' => 'Nama kelas harus berupa teks.',
            'nama.max' => 'Nama kelas maksimal 255 karakter.',
            'thumbnail.image' => 'File thumbnail harus berupa gambar.',
            'thumbnail.mimes' => 'Format gambar thumbnail harus: jpg,jpeg,png.',
            'thumbnail.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
