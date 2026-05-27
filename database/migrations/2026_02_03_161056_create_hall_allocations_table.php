<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hall_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_timetable_id')->constrained('exam_timetables')->onDelete('cascade');
            $table->foreignId('exam_hall_id')->constrained()->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->string('teacher_role')->default('invigilator'); // invigilator, chief_invigilator
            $table->timestamps();
            
            $table->unique(['exam_timetable_id', 'exam_hall_id']);
            $table->unique(['exam_timetable_id', 'teacher_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_allocations');
    }
};