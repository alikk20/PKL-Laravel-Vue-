<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    //Read Siswa
    public function show(Request $request)
    {
        $student = Auth::guard('student')->user();
        if ($student) {
            return response()->json($student);
        }

        return response()->json(['message' => 'User tidak ditemukan'], 404);
    }
    //Update Siswa
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
            'confirm_password' => 'nullable|string',
            'foto' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        DB::beginTransaction();
        try {
            // Update email, alamat, kontak
            if ($request->filled('email')) {
                $user->email = $request->email;
            }

            if ($request->filled('alamat')) {
                $user->alamat = $request->alamat;
            }

            if ($request->filled('kontak')) {
                $user->kontak = $request->kontak;
            }

            // Update password jika dikirim
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $user->password)) {
                    return response()->json(['message' => 'Password lama salah'], 400);
                }
                $user->password = Hash::make($request->new_password);
            }

            // Upload foto jika ada
            if ($request->hasFile('foto')) {
                $foto = $request->file('foto')->store('profile', 'public');
                $user->image = $foto; // pastikan kolom `image` ada di tabel students
            }

            $user->save();
            DB::commit();

            return response()->json([
                'message' => 'Profil berhasil diperbarui',
                'image' => $user->image // untuk update preview di frontend
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui profil',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Delete Siswa
    public function destroy(Request $request)
    {
        DB::beginTransaction();

        try {
            $student = Auth::guard('student')->user();
            if ($student) {
                $student->delete();
                DB::commit();
                return response()->json(['message' => 'Akun siswa berhasil dihapus']);
            }

            $teacher = Auth::guard('teacher')->user();
            if ($teacher) {
                $teacher->delete();
                DB::commit();
                return response()->json(['message' => 'Akun guru berhasil dihapus']);
            }

            DB::rollBack();
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
