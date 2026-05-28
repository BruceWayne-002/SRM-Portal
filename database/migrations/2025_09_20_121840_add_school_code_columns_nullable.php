<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            if (Schema::hasTable($tableName)) {

                // CHECK COLUMN DOES NOT EXIST
                if (!Schema::hasColumn($tableName, 'school_code')) {

                    Schema::table($tableName, function (Blueprint $table) {

                        $table->string('school_code')
                            ->nullable()
                            ->after('id');

                    });

                }

            }

        }
    }

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

            if (Schema::hasTable($tableName)) {

                if (Schema::hasColumn($tableName, 'school_code')) {

                    Schema::table($tableName, function (Blueprint $table) {

                        $table->dropColumn('school_code');

                    });

                }

            }

        }
    }
};