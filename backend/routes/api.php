<?php

use App\Models\Student;
use App\Models\Teacher;
use App\Models\Industry;
use App\Models\Internship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Registration;
use App\Http\Controllers\Authentications;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\InternshipController;

// API FOR FETCH
Route::get('/students', function () {
    return response()->json(Student::all());
});

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

Route::middleware('auth:student')->get('internship/cek', function (Request $request) {
    $student = $request->user();    
    $sudahDaftar = Internship::where('siswa_id', $student->id)->exists();
    return response()->json(['sudah_daftar' => $sudahDaftar]);
});
//END API FOR FETCH

//API LOGIN
Route::post('/login', [Authentications::class, 'login']);

//API INDUSTRI
Route::get('industry/{id}', [IndustryController::class, 'show']);//read industri
Route::middleware('auth:student')->group(function () {
    Route::post('industry', [IndustryController::class, 'store']);//create industri
    Route::put('industry/{id}', [IndustryController::class, 'update']);//update industri
    Route::delete('industry/{id}', [IndustryController::class, 'destroy']);//delete industri
});

//API PKL
Route::get('internship/{id}', [InternshipController::class, 'show']);//read industri
Route::middleware('auth:student')->group(function () {
    Route::post('/internships', [InternshipController::class, 'store']);//create industri
    Route::put('/internships/{id}', [InternshipController::class, 'update']);//update industri
    Route::delete('/internships/{id}', [InternshipController::class, 'destroy']);//delete industri
});

//API SISWA
Route::post('/register-siswa', [Registration::class, 'registersiswa']);//create siswa
Route::middleware(['auth:student'])->group(function () {
    Route::get('/profile', [StudentController::class, 'show']);//read siswa
    Route::put('/profile', [StudentController::class, 'update']);//update siswa
    Route::delete('/profile', [StudentController::class, 'destroy']);//delete siswa
});

// API GURU
Route::post('/register-guru', [Registration::class, 'registerguru']);//create guru
Route::middleware(['auth:teacher'])->group(function () {
    Route::get('/profile/teacher', [TeacherController::class, 'show']);//read guru
    Route::put('/profile/teacher', [TeacherController::class, 'update']);//update guru
    Route::delete('/profile/teacher', [TeacherController::class, 'destroy']);//delete guru
});