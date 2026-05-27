<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Create subjects table
     * 
     * This table stores information about academic subjects
     * including marks breakdown, class details, and teacher assignments
     * 
     * Command to run: php artisan migrate
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            // Primary key - Auto-incrementing ID
            $table->id();
            
            // Basic subject information
            $table->string('name');           // Subject name (e.g., Mathematics, Science)
            $table->string('code')->unique(); // Unique subject code (e.g., MATH101, SCI201)
            
            // Marks information
            $table->integer('total_marks')->nullable();     // Total marks for the subject
            $table->integer('passing_marks')->nullable();   // Minimum marks required to pass
            $table->integer('internal_marks')->nullable();  // Internal/continuous assessment marks
            $table->integer('external_marks')->nullable();  // External/final exam marks
            
            // Duration and school reference
            $table->integer('duration_hours')->nullable(); // Exam/total duration in hours
            $table->string('school_code');                 // Reference to the school
            
            // Class/grade details
            $table->year('year')->nullable();              // Academic year (e.g., 2024)
            $table->string('class_name', 100)->nullable(); // Class name (e.g., Class 10, Grade 11)
            $table->string('section', 50)->nullable();     // Section (e.g., A, B, Science Section)
            $table->string('grade', 10)->nullable();       // Grade (e.g., A, B+, Pass)
            
            // Teacher assignment
            $table->foreignId('teacher_id')
                  ->nullable()
                  ->constrained('teachers')  // References teachers table
                  ->nullOnDelete();          // Set to null if teacher is deleted
            
            // Timestamps for record keeping
            $table->timestamps();            // created_at and updated_at columns
            
            // Foreign key constraints
            // Links to schools table via school_code
            $table->foreign('school_code')
                  ->references('code')
                  ->on('schools')
                  ->onDelete('cascade');     // Delete subjects if school is deleted
        });
        
        // Terminal message for developers
        echo "\033[32m✓ Subjects table created successfully!\033[0m\n";
        echo "\033[33mColumns created: id, name, code, total_marks, passing_marks,\n";
        echo "internal_marks, external_marks, duration_hours, school_code,\n";
        echo "year, class_name, section, grade, teacher_id, timestamps\033[0m\n";
    }

    /**
     * Reverse the migrations - Drop subjects table
     * 
     * This will completely remove the subjects table and all its data
     * 
     * Command to run: php artisan migrate:rollback
     * or: php artisan migrate:rollback --step=1
     */
    public function down(): void
    {
        // Drop the entire subjects table
        Schema::dropIfExists('subjects');
        
        // Terminal message for developers
        echo "\033[31m✗ Subjects table dropped\033[0m\n";
        echo "\033[33mAll subject data has been removed\033[0m\n";
    }
};