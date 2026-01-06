<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Soal extends Model
{
    protected $fillable = [
        'materi_id',
        'pertanyaan',
        'jawaban_a',
        'jawaban_b',
        'jawaban_c',
        'jawaban_d',
        'jawaban_benar',
    ];

    public function materi()
    {
        return $this->belongsTo(Materi::class);
    }

    public function jawabanUsers()
    {
        return $this->hasMany(JawabanUser::class);
    }
}