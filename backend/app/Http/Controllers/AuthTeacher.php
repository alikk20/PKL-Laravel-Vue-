<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthTeacher extends Controller
{
    public function registerguru(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'id_number' => 'required|string|unique:teachers,nip',
            'gender' => 'required|in:Laki-laki,Perempuan',
            'alamat' => 'required|string',
            'kontak' => 'required|string',
            'password' => 'required|min:6'
        ]);

        $email = $request->id_number . '@gmail.com';

        $user = DB::transaction(function () use ($request, $email) {
            $data = [
                'nama' => $request->nama,
                'gender' => $request->gender,
                'alamat' => $request->alamat,
                'kontak' => $request->kontak,
                'email' => $email,
                'password' => Hash::make($request->password),
                'nip' => $request->id_number,
            ];

            $user = Teacher::create($data);
            $user->assignRole('teacher');

            return $user;
        });

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user
        ]);
    }
    

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
