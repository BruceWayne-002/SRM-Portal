<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DefaultSchoolSeeder extends Seeder
{
    public function run()
    {
        // Only insert if SCH001 doesn't exist
        if (!DB::table('schools')->where('code', 'SCH001')->exists()) {
            DB::table('schools')->insert([
                'code' => 'SCH001',
                'name' => 'Default School',
                'address' => '123 Main St',
                'email' => 'info@defaultschool.com',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Assign school_code to existing tables
        $tables = [
            'users', 'teachers', 'students', 'fee_setups', 'homeworks', 'marks',
            'notices', 'payments', 'achievements', 'student_attendances', 'student_leaves',
            'student_timetables', 'teacher_attendances', 'teacher_leaves', 'teacher_timetables',
            'timetables', 'books'
        ];

        foreach ($tables as $table) {
            DB::table($table)->update(['school_code' => 'SCH001']);
        }
    }
}
