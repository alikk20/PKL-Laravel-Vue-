<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::unprepared('
            CREATE TRIGGER update_status_pkl_delete
            AFTER DELETE ON internships
            FOR EACH ROW
            BEGIN
                IF NOT EXISTS (
                    SELECT 1 FROM internships WHERE siswa_id = OLD.siswa_id
                ) THEN
                    UPDATE students
                    SET status_pkl = 0
                    WHERE id = OLD.siswa_id;
                END IF;
            END
        ');
    }

    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_status_pkl_delete');
    }
};
