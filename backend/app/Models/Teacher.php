<?php

namespace App\Models;

use App\Models\Internship;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class Teacher extends Authenticatable implements JWTSubject
{
    use HasRoles;

    protected $table = 'teachers';

    protected $fillable = [
        'nama',
        'nip',
        'gender',
        'alamat',
        'kontak',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
    ];

    public function internship()
    {
        return $this->hasMany(Internship::class);
    }

    // JWT implementation
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
