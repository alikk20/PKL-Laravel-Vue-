<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TeacherController extends Controller
{
    //Read Guru
    public function show()
    {
        $teacher = auth('teacher')->user();

        if (!$teacher) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        return response()->json($teacher);
    }
    //Update Guru
    public function update(Request $request)
    {
        $teacher = auth('teacher')->user();

        if (!$teacher) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        $request->validate([
            'nama' => 'nullable|string|max:255',
            'gender' => 'nullable|in:Laki-laki,Perempuan',
            'alamat' => 'nullable|string',
            'kontak' => 'nullable|string',
            'old_password' => 'nullable|string',
            'new_password' => 'nullable|string|min:6|required_with:old_password|same:confirm_password',
            'confirm_password' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            if ($request->filled('nama')) {
                $teacher->nama = $request->nama;
            }

            if ($request->filled('gender')) {
                $teacher->gender = $request->gender;
            }

            if ($request->filled('alamat')) {
                $teacher->alamat = $request->alamat;
            }

            if ($request->filled('kontak')) {
                $teacher->kontak = $request->kontak;
            }

            // Update password jika ada
            if ($request->filled('old_password')) {
                if (!Hash::check($request->old_password, $teacher->password)) {
                    return response()->json(['message' => 'Password lama salah'], 400);
                }

                $teacher->password = Hash::make($request->new_password);
            }

            $teacher->save();
            DB::commit();

            return response()->json(['message' => 'Profil guru berhasil diperbarui']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal memperbarui data',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    //Delete Guru
    public function destroy()
    {
        $teacher = auth('teacher')->user();

        if (!$teacher) {
            return response()->json(['message' => 'User tidak ditemukan'], 404);
        }

        DB::beginTransaction();
        try {
            $teacher->delete();
            DB::commit();

            return response()->json(['message' => 'Akun guru berhasil dihapus']);
        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus akun',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
