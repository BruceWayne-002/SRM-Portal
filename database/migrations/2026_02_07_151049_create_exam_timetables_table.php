// database/migrations/xxxx_xx_xx_create_exam_timetables_table.php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExamTimetablesTable extends Migration
{
    public function up()
    {
        Schema::create('exam_timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('class_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->integer('order')->default(0); // For ordering multiple sessions in a day
            $table->string('session_name')->nullable(); // Morning/Afternoon session
            $table->integer('student_count')->default(0);
            $table->text('instructions')->nullable();
            $table->string('invigilator')->nullable();
            $table->timestamps();
            
            // Unique constraint to prevent double booking
            $table->unique(['class_id', 'date', 'start_time']);
            $table->unique(['exam_id', 'date', 'start_time']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('exam_timetables');
    }
}