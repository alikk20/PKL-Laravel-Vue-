<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthControllerStudent extends Controller
{
    public function registersiswa(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'id_number' => 'required|string|unique:students,nis',
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
                'nis' => $request->id_number,
                'image' => 'default.png',
                'status_pkl' => 0,
            ];

            $user = Student::create($data);

            return $user;
        });

        return response()->json([
            'message' => 'Register berhasil',
            'user' => $user
        ]);
    }
}
