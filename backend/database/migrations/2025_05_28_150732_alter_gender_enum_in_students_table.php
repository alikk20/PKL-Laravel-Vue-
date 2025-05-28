<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        // Ubah enum gender jadi 'L' dan 'P'
        DB::statement("ALTER TABLE students MODIFY gender ENUM('L', 'P') NOT NULL");
    }

    public function down()
    {
        DB::statement("ALTER TABLE students MODIFY gender ENUM('Laki-laki', 'Perempuan') NOT NULL");
    }
};
