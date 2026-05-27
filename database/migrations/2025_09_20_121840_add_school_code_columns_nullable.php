<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $tables = [
            'users', 'teachers', 'students', 'fee_setups', 'homeworks', 'marks',
            'notices', 'payments', 'achievements', 'student_attendances', 'student_leaves',
            'student_timetables', 'teacher_attendances', 'teacher_leaves', 'teacher_timetables',
            'timetables', 'books'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (!Schema::hasColumn($tableName, 'school_code')) {
                    $table->string('school_code')->nullable()->after('id');
                }
            });
        }
    }

    public function down(): void
    {
        $tables = [
            'users', 'teachers', 'students', 'fee_setups', 'homeworks', 'marks',
            'notices', 'payments', 'achievements', 'student_attendances', 'student_leaves',
            'student_timetables', 'teacher_attendances', 'teacher_leaves', 'teacher_timetables',
            'timetables', 'books'
        ];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                if (Schema::hasColumn($tableName, 'school_code')) {
                    $table->dropColumn('school_code');
                }
            });
        }
    }
};
