<?php

namespace App\Http\Controllers;

use App\Models\Industry;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class IndustryController extends Controller
{
    //Read Industri
    public function show($id)
    {
        $industry = Industry::find($id);

        if (!$industry) {
            return response()->json(['message' => 'Data tidak ditemukan'], 404);
        }

        return response()->json([
            'message' => 'Detail data industri',
            'data' => $industry
        ]);
    }
    //Create Industri
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'bidang_usaha' => 'required|string|max:255',
            'alamat' => 'required|string|max:255',
            'kontak' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:industries,email',
        ]);
    
        // Ambil ID siswa yang sedang login
        $userId = auth()->guard('student')->id();
    
        $industry = Industry::create([
            ...$validated,
            'siswa_id' => $userId,
        ]);
    
        return response()->json(['message' => 'Created', 'data' => $industry], 201);
    }
    //Update Industri
    public function update(Request $request, $id)
    {
        $industry = Industry::findOrFail($id);
    
        if ($industry->siswa_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $validated = $request->validate([
            'nama' => 'sometimes|string|max:255',
            'bidang_usaha' => 'sometimes|string|max:255',
            'alamat' => 'sometimes|string|max:255',
            'kontak' => 'sometimes|string|max:255',
            'email' => 'sometimes|email|max:255|unique:industries,email,' . $industry->id,
        ]);
    
        $industry->update($validated);
    
        return response()->json(['message' => 'Data updated successfully', 'data' => $industry]);
    }
    //Delete Industri
    public function destroy($id)
    {
        $industry = Industry::findOrFail($id);
    
        if ($industry->siswa_id !== auth()->id()) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
    
        $industry->delete();
    
        return response()->json(['message' => 'Data deleted successfully']);
    }
    
}

