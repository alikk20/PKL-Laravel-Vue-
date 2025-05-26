<?php

namespace App\Http\Controllers;

use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class InternshipController extends Controller
{
    //Read Internships
    public function show($id)
    {
        $internship = Internship::with(['industry:id,nama', 'teacher:id,nama'])
            ->find($id);

        if (!$internship) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail data PKL',
            'data' => $internship
        ]);
    }
    //Create Internships
    public function store(Request $request)
    {
        $request->validate([
            'industri_id' => 'required|exists:industries,id',
            'guru_id' => 'required|exists:teachers,id',
            'mulai' => 'required|date',
            'selesai' => 'required|date|after_or_equal:mulai',
        ]);

        $student = Auth::guard('student')->user();

        DB::beginTransaction();

        try {
            $internship = Internship::create([
                'siswa_id' => $student->id,
                'industri_id' => $request->industri_id,
                'guru_id' => $request->guru_id,
                'mulai' => $request->mulai,
                'selesai' => $request->selesai,
            ]);

            DB::commit();

            return response()->json([
                'message' => 'Data PKL berhasil ditambahkan',
                'data' => $internship,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menyimpan data', 'error' => $e->getMessage()], 500);
        }
    }
    //Update Internships
    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'sometimes|exists:teachers,id',
            'industri_id' => 'sometimes|exists:industries,id',
            'mulai' => 'sometimes|date',
            'selesai' => 'sometimes|date|after_or_equal:mulai',
        ]);        
    
        $internship = Internship::findOrFail($id);
    
        if ($internship->siswa_id !== auth()->id()) {
            return response()->json([
                'message' => 'Anda tidak memiliki izin untuk mengedit data ini'
            ], 403);
        }        
    
        DB::beginTransaction();
    
        try {
            $internship->fill($request->only([
                'guru_id',
                'industri_id',
                'mulai',
                'selesai',
            ]));            
    
            $internship->save();
    
            DB::commit(); // Commit jika sukses
    
            return response()->json([
                'message' => 'Data PKL berhasil diperbarui',
                'data' => $internship
            ]);
        } catch (\Exception $e) {
            DB::rollBack(); // Rollback kalau error
    
            return response()->json([
                'message' => 'Gagal memperbarui data PKL',
                'error' => $e->getMessage(),
            ], 500);
        }
    }    
    //Delete Internships
    public function destroy($id)
    {
        $student = Auth::guard('student')->user();

        $internship = Internship::where('id', $id)
            ->where('siswa_id', $student->id)
            ->first();

        if (!$internship) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        DB::beginTransaction();

        try {
            $internship->delete();
            DB::commit();

            return response()->json(['message' => 'Data berhasil dihapus']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Gagal menghapus data', 'error' => $e->getMessage()], 500);
        }
    }

}
