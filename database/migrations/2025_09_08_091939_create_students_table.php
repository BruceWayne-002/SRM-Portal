<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('class');
            $table->string('section', 1);
            $table->integer('roll_no');
            $table->string('emis_no')->nullable();
            $table->string('aadhar_no', 12)->nullable();
            $table->date('dob');
            $table->date('admission_date');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('medium');
            $table->string('blood_group', 3)->nullable();
            $table->string('parent_name');
            $table->string('mother_name')->nullable();
            $table->string('occupation')->nullable();
            $table->string('contact');
            $table->string('alt_contact')->nullable();
            $table->text('address');
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
