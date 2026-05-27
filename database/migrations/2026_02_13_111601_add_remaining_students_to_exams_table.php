<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'remaining_students')) {
                $table->integer('remaining_students')->default(0)->after('student_count');
            }
            if (!Schema::hasColumn('exams', 'allocated_students')) {
                $table->integer('allocated_students')->default(0)->after('student_count');
            }
            if (!Schema::hasColumn('exams', 'allocation_status')) {
                $table->string('allocation_status')->default('pending')->after('allocated_students');
            }
        });
    }

    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropColumn(['remaining_students', 'allocated_students', 'allocation_status']);
        });
    }
};