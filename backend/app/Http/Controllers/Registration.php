<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class Registration extends Controller
{
    // Create Siswa
    public function registersiswa(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'id_number' => 'required|string|unique:students,nis',
            'gender' => 'required|in:L,P',
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
                'nis' => $request->id_number,
                'image' => 'default.png',
                'status_pkl' => 0,
            ];

            $user = Student::create($data);
            $user->assignRole('student');

            return $user;
        });

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user
        ]);
    }

    //Create Guru
    public function registerguru(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'id_number' => 'required|string|unique:teachers,nip',
            'gender' => 'required|in:L,P',
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
}
