<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_timetables', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_timetables', 'exam_date')) {
                $table->date('exam_date')->nullable()->after('exam_id');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_timetables', function (Blueprint $table) {
            if (Schema::hasColumn('exam_timetables', 'exam_date')) {
                $table->dropColumn('exam_date');
            }
        });
    }
};