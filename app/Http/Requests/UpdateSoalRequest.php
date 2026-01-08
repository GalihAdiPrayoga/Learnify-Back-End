<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSoalRequest extends FormRequest
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
            'materi_id' => 'sometimes|exists:materis,id',
            'pertanyaan' => 'sometimes|string',
            'jawaban_a' => 'sometimes|string',
            'jawaban_b' => 'sometimes|string',
            'jawaban_c' => 'sometimes|string',
            'jawaban_d' => 'sometimes|string',
            'jawaban_benar' => 'sometimes|in:a,b,c,d',
        ];
    }
}
