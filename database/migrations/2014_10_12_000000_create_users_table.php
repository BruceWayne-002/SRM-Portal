<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');

            // 👇 Add this
            $table->enum('role', ['Admin', 'Teacher', 'Student', 'Parent'])->default('Student');

            // Extra fields if needed for teacher/student/parent login
            $table->string('employee_id')->nullable();   // for teachers
            $table->string('register_no')->nullable();   // for students
            $table->date('dob')->nullable();             // for students
            $table->unsignedBigInteger('student_id')->nullable(); // for parents

            $table->rememberToken();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
