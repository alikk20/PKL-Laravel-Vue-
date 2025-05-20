<?php

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Industry;
use App\Models\Internship;
use Illuminate\Http\Request;
use App\Http\Controllers\AuthTeacher;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InternshipController;
use App\Http\Controllers\AuthControllerStudent;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::get('/industry', function () {
    return response()->json(Industry::all());
});
Route::get('/teachers', function () {
    return response()->json(Teacher::all());
});
Route::get('/internships', function () {
    $internships = Internship::with(['student', 'teacher', 'industry'])->get();
    return response()->json($internships);
});
Route::middleware('auth:student')->get('/cekindustri', function (Request $request) {
    $user = $request->user();
    $hasIndustry = Industry::where('siswa_id', $user->id)->exists();
    return response()->json(['hasIndustry' => $hasIndustry]);
});

Route::post('/register-siswa', [AuthControllerStudent::class, 'registersiswa']);
Route::post('/register-guru', [AuthTeacher::class, 'registerguru']);
Route::post('/login', [AuthTeacher::class, 'login']);

Route::middleware('auth:student')->group(function () {
    Route::post('industry', [IndustryController::class, 'store']);
    Route::put('industry/{id}', [IndustryController::class, 'update']);    
    Route::delete('industry/{id}', [IndustryController::class, 'destroy']);
});

Route::middleware('auth:student')->group(function () {
    Route::post('/internships', [InternshipController::class, 'store']);
    Route::put('/internships/{id}', [InternshipController::class, 'update']);
    Route::delete('/internships/{id}', [InternshipController::class, 'destroy']);
});

Route::middleware(['auth:student'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
});