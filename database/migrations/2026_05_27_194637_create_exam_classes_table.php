<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run migrations.
     */
    public function up(): void
    {
        // CREATE ONLY IF NOT EXISTS
        if (!Schema::hasTable('exam_classes')) {

            Schema::create('exam_classes', function (Blueprint $table) {

                $table->id();

                $table->unsignedBigInteger('exam_id')->nullable();

                $table->string('class_name');

                $table->timestamps();

                // INDEX
                $table->index('exam_id');

            });

        }
    }

    /**
     * Reverse migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('exam_classes');
    }
};