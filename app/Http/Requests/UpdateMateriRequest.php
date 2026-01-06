<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMateriRequest extends FormRequest
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
            'kelas_id' => 'sometimes|required|exists:kelas,id',
            'judul' => 'sometimes|required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'konten' => 'sometimes|required|string', 
        ];
    }

    public function messages(): array
    {
        return [
            'kelas_id.required' => 'Kelas wajib dipilih.',
            'kelas_id.exists'   => 'Kelas yang dipilih tidak valid.',
            'judul.required'    => 'Judul materi wajib diisi.',
            'judul.string'      => 'Judul materi harus berupa teks.',
            'judul.max'         => 'Judul materi maksimal 255 karakter.',
            'deskripsi.string'  => 'Deskripsi materi harus berupa teks.',
            'deskripsi.max'     => 'Deskripsi materi maksimal 500 karakter.',
            'konten.required'   => 'Konten materi wajib diisi.',
            'konten.string'     => 'Konten materi harus berupa teks.',
        ];
    }
}
