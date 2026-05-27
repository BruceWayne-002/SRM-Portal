<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExamAllocationsToExamHalls extends Migration
{
    public function up()
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_halls', 'exam_allocations')) {
                $table->json('exam_allocations')->nullable()->after('seat_assignments');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            if (Schema::hasColumn('exam_halls', 'exam_allocations')) {
                $table->dropColumn('exam_allocations');
            }
        });
    }
}