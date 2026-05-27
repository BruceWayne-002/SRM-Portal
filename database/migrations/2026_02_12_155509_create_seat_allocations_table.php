<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('seat_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_hall_id')->constrained()->onDelete('cascade');
            $table->foreignId('exam_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('seat_number');
            $table->string('row')->nullable();
            $table->integer('column')->nullable();
            $table->enum('allocation_type', ['auto', 'manual'])->default('auto');
            $table->foreignId('allocated_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('allocated_at')->useCurrent();
            $table->enum('status', ['allocated', 'cancelled', 'attended', 'absent'])->default('allocated');
            $table->timestamps();
            
            $table->unique(['exam_hall_id', 'exam_id', 'seat_number'], 'unique_seat_allocation');
            $table->unique(['exam_id', 'student_id'], 'unique_student_exam');
        });
    }

    public function down()
    {
        Schema::dropIfExists('seat_allocations');
    }
};