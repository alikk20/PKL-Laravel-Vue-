<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Membuat stored function get_gender_desc
        DB::unprepared('
            CREATE FUNCTION get_gender_desc(g CHAR(1))
            RETURNS VARCHAR(20)
            DETERMINISTIC
            BEGIN
                IF g = "L" THEN
                    RETURN "Laki-laki";
                ELSEIF g = "P" THEN
                    RETURN "Perempuan";
                ELSE
                    RETURN "Tidak diketahui";
                END IF;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP FUNCTION IF EXISTS get_gender_desc');
    }
};
