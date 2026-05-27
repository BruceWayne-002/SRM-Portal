<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendances')) {
            Schema::create('attendances', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('teacher_id');
                $table->unsignedBigInteger('student_id');
                $table->unsignedBigInteger('allocation_id');
                $table->unsignedBigInteger('exam_id');
                $table->unsignedBigInteger('hall_id');
                $table->date('exam_date');
                $table->string('session', 20);
                $table->enum('status', ['present', 'absent', 'late'])->default('absent');
                $table->text('remarks')->nullable();
                $table->unsignedBigInteger('marked_by')->nullable();
                $table->timestamp('marked_at')->nullable();
                $table->timestamps();

                // Foreign keys
                $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
                $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
                $table->foreign('allocation_id')->references('id')->on('exam_hall_allocations')->onDelete('cascade');
                $table->foreign('exam_id')->references('id')->on('exams')->onDelete('cascade');
                $table->foreign('hall_id')->references('id')->on('halls')->onDelete('cascade');
                $table->foreign('marked_by')->references('id')->on('users')->onDelete('set null');

                // Indexes
                $table->index(['exam_date', 'session']);
                $table->index(['allocation_id', 'student_id']);
                $table->index('status');
                
                // Unique constraint
                $table->unique(['allocation_id', 'student_id'], 'unique_attendance');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};