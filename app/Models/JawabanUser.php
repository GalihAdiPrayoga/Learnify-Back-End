<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JawabanUser extends Model
{
    protected $guarded = [];

    public function hasilUjian()
    {
        return $this->belongsTo(HasilUjian::class);
    }

    public function soal()
    {
        return $this->belongsTo(Soal::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
