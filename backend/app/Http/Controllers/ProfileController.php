<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    
    public function show(Request $request)
    {
        $student = Auth::guard('student')->user();
        if ($student) {
            return response()->json($student);
        }

        $teacher = Auth::guard('teacher')->user();
        if ($teacher) {
            return response()->json($teacher);
        }

        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }

    public function update(Request $request)
    {
        $user = auth('student')->user();
    
        if (!$user) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }
    
        $request->validate([
            'email' => 'nullable|email',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|required_with:old_password|same:confirm_password',
            'confirm_password' => 'nullable|string'
        ]);
    
        DB::beginTransaction();
        try {
            // Update email jika dikirim
            if ($request->filled('email')) {
                $user->email = $request->email;
            }
    
            // Update alamat dan kontak
            if ($request->filled('alamat')) {
                $user->alamat = $request->alamat;
            }
    
            if ($request->filled('kontak')) {
                $user->kontak = $request->kontak;
            }
    
            // Jika ingin ganti password
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json(['message' => 'Password lama salah'], 400);
                }
                $user->password = Hash::make($request->new_password);
            }
    
            $user->save();
            DB::commit();
    
            return response()->json(['message' => 'Profil berhasil diperbarui']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal memperbarui profil', 'error' => $e->getMessage()], 500);
        }
    }
}
