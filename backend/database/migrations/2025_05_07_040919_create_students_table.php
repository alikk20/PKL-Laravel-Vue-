<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id()->primary();
            $table->string('nama');
            $table->string('nis')->unique();
            $table->enum('gender', ['Laki-laki', 'Perempuan']);
            $table->string('alamat');
            $table->string('kontak');
            $table->string('email')->unique();
            $table->enum('status_pkl', ['Proses', 'Diterima'])->default('Proses');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
