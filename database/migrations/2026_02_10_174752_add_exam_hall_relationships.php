<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            $table->foreignId('teacher_id')->nullable()->constrained('teachers')->onDelete('set null');
            $table->json('exam_ids')->nullable();
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->string('exam_type')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn(['teacher_id', 'exam_ids', 'exam_date', 'start_time', 'end_time', 'exam_type']);
        });
    }
};