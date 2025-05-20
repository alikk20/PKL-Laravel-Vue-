<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Industry extends Model
{
    protected $table = 'industries';
    protected $fillable = [
        'nama',
        'bidang_usaha',
        'alamat',
        'kontak',
        'email',
        'siswa_id',
    ];
    public function internship()
    {
        return $this->hasMany(Internship::class);
    }
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
