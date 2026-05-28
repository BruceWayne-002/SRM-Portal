<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // CHECK TABLE EXISTS
        if (!Schema::hasTable('attendances')) {

            Schema::create('attendances', function (Blueprint $table) {

                $table->id();

                // SAFE BIGINTS
                $table->unsignedBigInteger('teacher_id')->nullable();

                $table->unsignedBigInteger('student_id')->nullable();

                $table->unsignedBigInteger('allocation_id')->nullable();

                $table->unsignedBigInteger('exam_id')->nullable();

                $table->unsignedBigInteger('hall_id')->nullable();

                $table->date('exam_date');

                $table->string('session', 20);

                $table->enum('status', ['present', 'absent', 'late'])
                    ->default('absent');

                $table->text('remarks')->nullable();

                $table->unsignedBigInteger('marked_by')->nullable();

                $table->timestamp('marked_at')->nullable();

                $table->timestamps();

                $table->softDeletes();

                // INDEXES
                $table->index(['exam_date', 'session']);

                $table->index(['allocation_id', 'student_id']);

                $table->index('status');

                // UNIQUE
                $table->unique(
                    ['allocation_id', 'student_id'],
                    'unique_attendance_per_student'
                );
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};