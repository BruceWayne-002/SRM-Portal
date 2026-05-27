<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('teachers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('employee_id')->unique();
            $table->date('dob');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('email')->unique();
            $table->string('phone');
            $table->text('address');
            $table->string('qualification')->nullable();
            $table->string('parent_name')->nullable();
            $table->date('joining_date');
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->string('aadhar_no', 12)->nullable();
            $table->string('blood_group', 3)->nullable();
            $table->integer('class')->nullable();
            $table->string('section', 1)->nullable();
            $table->enum('role', ['Class-incharge', 'Teacher', 'Office Staff']);
            $table->string('photo')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
