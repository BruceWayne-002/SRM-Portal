<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_hall_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('hall_allocation_id')->constrained()->onDelete('cascade');
            $table->integer('seat_number');
            $table->string('seat_position'); // e.g., "A1", "B3"
            $table->string('hall_ticket_number')->unique();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
            
            $table->unique(['hall_allocation_id', 'seat_number']);
            $table->unique(['hall_allocation_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_hall_allocations');
    }
};