<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;


class Authentications extends Controller
{
    public function login(Request $request)
    {
        // Validasi input request
        $request->validate([
            'id_number' => 'required|string',
            'password' => 'required|string',
        ]);
        
        // Login sebagai guru
        $teacher = Teacher::where('nip', $request->id_number)->first();
        if ($teacher && Hash::check($request->password, $teacher->password)) {
            $role = $teacher->getRoleNames()->first(); // Ambil role dari Spatie
            $token = JWTAuth::fromUser($teacher);
        
            return response()->json([
                'message' => 'Login berhasil',
                'user' => $teacher,
                'role' => $role, // Bisa null jika belum di-assign
                'token' => $token
            ]);
        }
        
        // Login sebagai siswa
        $student = Student::where('nis', $request->id_number)->first();
        if ($student && Hash::check($request->password, $student->password)) {
            $role = $student->getRoleNames()->first(); // Ambil role dari Spatie
            $token = JWTAuth::fromUser($student);
        
            return response()->json([
                'message' => 'Login berhasil',
                'user' => $student,
                'role' => $role, // Bisa null jika belum di-assign
                'token' => $token
            ]);
        }
        
        return response()->json(['message' => 'ID atau password salah.'], 401);
    } 
}
