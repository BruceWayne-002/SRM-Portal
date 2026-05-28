<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tables = [
            'users',
            'teachers',
            'students',
            'fee_setups',
            'homeworks',
            'marks',
            'notices',
            'payments',
            'achievements',
            'student_attendances',
            'student_leaves',
            'student_timetables',
            'teacher_attendances',
            'teacher_leaves',
            'teacher_timetables',
            'timetables',
            'books'
        ];

        foreach ($tables as $tableName) {

            // CHECK TABLE EXISTS
            if (!Schema::hasTable($tableName)) {
                continue;
            }

            // CHECK COLUMN EXISTS
            if (!Schema::hasColumn($tableName, 'school_code')) {
                continue;
            }

            try {

                // CHECK IF FOREIGN KEY ALREADY EXISTS
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = ?
                    AND COLUMN_NAME = 'school_code'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ", [$tableName]);

                // SKIP IF FK ALREADY EXISTS
                if (count($foreignKeys) > 0) {
                    continue;
                }

                Schema::table($tableName, function (Blueprint $table) {

                    $table->foreign('school_code')
                        ->references('code')
                        ->on('schools')
                        ->onDelete('cascade');

                });

            } catch (\Exception $e) {

                // IGNORE ERRORS
                continue;

            }

        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'users',
            'teachers',
            'students',
            'fee_setups',
            'homeworks',
            'marks',
            'notices',
            'payments',
            'achievements',
            'student_attendances',
            'student_leaves',
            'student_timetables',
            'teacher_attendances',
            'teacher_leaves',
            'teacher_timetables',
            'timetables',
            'books'
        ];

        foreach ($tables as $tableName) {

            if (!Schema::hasTable($tableName)) {
                continue;
            }

            try {

                Schema::table($tableName, function (Blueprint $table) use ($tableName) {

                    $foreignKeyName = $tableName . '_school_code_foreign';

                    $table->dropForeign($foreignKeyName);

                });

            } catch (\Exception $e) {

                // IGNORE ERRORS
                continue;

            }

        }
    }
};