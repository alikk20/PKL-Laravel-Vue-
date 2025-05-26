<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUpdateStatusPklTrigger extends Migration
{
    public function up()
    {
        DB::unprepared('
            CREATE TRIGGER update_status_pkl_fix
            AFTER INSERT ON internships
            FOR EACH ROW
            BEGIN
                UPDATE students
                SET status_pkl = 1
                WHERE id = NEW.siswa_id;
            END
        ');
    }

    public function down()
    {
        DB::unprepared('DROP TRIGGER IF EXISTS update_status_pkl');
    }
};
