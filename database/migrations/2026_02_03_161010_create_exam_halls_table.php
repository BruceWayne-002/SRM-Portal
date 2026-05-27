<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_halls', function (Blueprint $table) {
            $table->id();
            $table->string('hall_name');
            $table->string('hall_code')->unique();
            $table->integer('capacity');
            $table->integer('rows');
            $table->integer('columns');
            $table->string('floor');
            $table->string('building');
            $table->string('school_code');
            $table->timestamps();
            
            $table->foreign('school_code')->references('code')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_halls');
    }
};