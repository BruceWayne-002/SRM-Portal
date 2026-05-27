<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('student_timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('class'); // 1–12
            $table->char('section', 1); // A–E
            $table->string('group')->nullable(); // only for class 11,12
            $table->string('day'); // Monday–Saturday
            $table->string('period'); // Period 1–8, Morning Special, Evening Special
            $table->string('subject'); // Subject name
            $table->unsignedBigInteger('teacher_id')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('set null');
        });

    }

    public function down(): void
    {
        Schema::dropIfExists('student_timetables');
    }
};
