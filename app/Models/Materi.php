<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Materi extends Model
{
    protected $fillable = [
        'judul',
        'deskripsi',
        'konten',
        'kelas_id',
    ];

    // RELASI
    public function kelas()
    {
        return $this->belongsTo(Kelas::class);
    }

    public function soals()
    {
        return $this->hasMany(Soal::class);
    }

    public function hasilUjian()
    {
        return $this->hasMany(HasilUjian::class);
    }

}
