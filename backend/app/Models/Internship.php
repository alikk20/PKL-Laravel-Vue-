<?php

namespace App\Models;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Industry;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Internship extends Model
{
    protected $table = 'internships';
    protected $fillable = [
        'siswa_id',
        'industri_id',
        'guru_id',
        'mulai',
        'selesai',
    ];
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'siswa_id');
    }
    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industri_id');
    }
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'guru_id');
    }
}
