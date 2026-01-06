<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProfileRequest extends FormRequest
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
        $id = $this->user()->id;
        return [
            'name' => 'nullable|string|max:255',
            'username' => "nullable|string|max:255|unique:users,username,$id",
            'email' => "nullable|email|unique:users,email,$id",
            'password' => 'nullable|min:6',
            'profile' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ];
    }
     public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'email.unique' => 'Email sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 8 karakter.',
            'profile.image' => 'File profil harus berupa gambar.',
            'profile.mimes' => 'Format gambar profil harus: jpeg,png,jpg,gif,webp.',
            'profile.max' => 'Ukuran gambar maksimal 2MB.',
        ];
    }
}
