<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teacher_timetables', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('teacher_id');
            $table->string('class'); // e.g., X, XII
            $table->string('section')->nullable(); // A, B, ...
            $table->string('group')->nullable(); // for 11/12 groups
            $table->enum('day', ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday']);
            $table->enum('period', ['Morning','1','2','3','4','5','6','7','8','Evening']);
            $table->string('subject')->nullable();
            $table->timestamps();

            $table->foreign('teacher_id')->references('id')->on('teachers')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_timetables');
    }
};
