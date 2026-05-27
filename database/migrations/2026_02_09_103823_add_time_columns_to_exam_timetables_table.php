<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_timetables', function (Blueprint $table) {
            // Check and add start_time if it doesn't exist
            if (!Schema::hasColumn('exam_timetables', 'start_time')) {
                $table->time('start_time')->nullable()->after('exam_date');
            }
            
            // Check and add end_time if it doesn't exist
            if (!Schema::hasColumn('exam_timetables', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_timetables', function (Blueprint $table) {
            if (Schema::hasColumn('exam_timetables', 'start_time')) {
                $table->dropColumn('start_time');
            }
            
            if (Schema::hasColumn('exam_timetables', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });
    }
};