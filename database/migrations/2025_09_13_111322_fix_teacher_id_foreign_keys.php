<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Fix teacher_id in teacher_attendances
        DB::table('teacher_attendances')
            ->get()
            ->each(function ($attendance) {
                $user = DB::table('users')->where('employee_id', DB::table('teachers')->where('id', $attendance->teacher_id)->value('employee_id'))->first();
                if ($user) {
                    DB::table('teacher_attendances')
                        ->where('id', $attendance->id)
                        ->update(['teacher_id' => $user->id]);
                } else {
                    // Remove invalid attendance
                    DB::table('teacher_attendances')->where('id', $attendance->id)->delete();
                }
            });

        // Fix teacher_id in teacher_leaves
        DB::table('teacher_leaves')
            ->get()
            ->each(function ($leave) {
                $user = DB::table('users')->where('employee_id', DB::table('teachers')->where('id', $leave->teacher_id)->value('employee_id'))->first();
                if ($user) {
                    DB::table('teacher_leaves')
                        ->where('id', $leave->id)
                        ->update(['teacher_id' => $user->id]);
                } else {
                    // Remove invalid leave
                    DB::table('teacher_leaves')->where('id', $leave->id)->delete();
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Nothing to reverse
    }
};
