<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->foreign('siswa_id')
                ->references('id')
                ->on('students')
                ->onDelete('cascade');

            $table->dropForeign(['industri_id']);
            $table->foreign('industri_id')
                ->references('id')
                ->on('industries')
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('internships', function (Blueprint $table) {
            $table->dropForeign(['siswa_id']);
            $table->foreign('siswa_id')
                ->references('id')
                ->on('students');

            $table->dropForeign(['industri_id']);
            $table->foreign('industri_id')
                ->references('id')
                ->on('industries');
        });
    }
};
