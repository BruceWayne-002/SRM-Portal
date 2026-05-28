<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // SKIP IF TABLE ALREADY EXISTS
        if (Schema::hasTable('hall_allocations')) {
            return;
        }

        Schema::create('hall_allocations', function (Blueprint $table) {

            $table->id();

            $table->unsignedBigInteger('exam_timetable_id')->nullable();

            $table->unsignedBigInteger('exam_hall_id')->nullable();

            $table->unsignedBigInteger('teacher_id')->nullable();

            $table->string('teacher_role')
                ->default('invigilator');

            $table->timestamps();

            $table->unique([
                'exam_timetable_id',
                'exam_hall_id'
            ]);

            $table->unique([
                'exam_timetable_id',
                'teacher_id'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hall_allocations');
    }
};