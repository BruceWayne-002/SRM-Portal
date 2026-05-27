<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('classes', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Class name (e.g., "Class 1")
            $table->string('code')->unique(); // Unique identifier
            $table->integer('numeric_value'); // For sorting (1-12)
            $table->text('description')->nullable();
            
            // Section fields (since you want them together)
            $table->string('section_name'); // Section name (e.g., "A", "B")
            $table->string('section_code')->unique(); // Section unique identifier
            $table->integer('capacity')->default(40);
            $table->string('room_number')->nullable();
            $table->integer('sort_order')->default(0);
            
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('is_active');
            $table->index('numeric_value');
            $table->index(['numeric_value', 'section_name']);
            $table->index(['is_active', 'numeric_value']);
        });

        // Remove the sections table creation
        // Schema::create('sections', function (Blueprint $table) {
        //     // Remove this
        // });

        // Update students table
        Schema::table('students', function (Blueprint $table) {
            $table->foreignId('class_id')->nullable()->constrained('classes');
            // Remove section_id since now class includes section
            // $table->foreignId('section_id')->nullable()->constrained();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign(['class_id']);
            $table->dropColumn(['class_id']);
        });
        
        Schema::dropIfExists('classes');
    }
};