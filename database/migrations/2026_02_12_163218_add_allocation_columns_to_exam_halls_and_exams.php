<?php
// TERMINAL: php artisan make:migration add_allocation_columns_to_exam_halls_and_exams
// RUN AFTER CREATING: php artisan migrate

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAllocationColumnsToExamHallsAndExams extends Migration
{
    public function up()
    {
        // Add columns to exam_halls table
        Schema::table('exam_halls', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_halls', 'total_students')) {
                $table->integer('total_students')->default(0)->after('exam_type');
            }
            if (!Schema::hasColumn('exam_halls', 'allocated_students')) {
                $table->integer('allocated_students')->default(0)->after('total_students');
            }
            if (!Schema::hasColumn('exam_halls', 'remaining_students')) {
                $table->integer('remaining_students')->default(0)->after('allocated_students');
            }
            if (!Schema::hasColumn('exam_halls', 'allocation_status')) {
                $table->string('allocation_status')->default('pending')->after('remaining_students');
            }
            if (!Schema::hasColumn('exam_halls', 'seat_assignments')) {
                $table->json('seat_assignments')->nullable()->after('allocation_status');
            }
        });
        
        // Add columns to exams table
        Schema::table('exams', function (Blueprint $table) {
            if (!Schema::hasColumn('exams', 'hall_id')) {
                $table->unsignedBigInteger('hall_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('exams', 'allocated_students')) {
                $table->integer('allocated_students')->default(0)->after('hall_id');
            }
            if (!Schema::hasColumn('exams', 'remaining_students')) {
                $table->integer('remaining_students')->default(0)->after('allocated_students');
            }
            if (!Schema::hasColumn('exams', 'allocation_status')) {
                $table->string('allocation_status')->default('pending')->after('remaining_students');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            $columns = ['total_students', 'allocated_students', 'remaining_students', 'allocation_status', 'seat_assignments'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('exam_halls', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
        
        Schema::table('exams', function (Blueprint $table) {
            $columns = ['hall_id', 'allocated_students', 'remaining_students', 'allocation_status'];
            foreach ($columns as $column) {
                if (Schema::hasColumn('exams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}