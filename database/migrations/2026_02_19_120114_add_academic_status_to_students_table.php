<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'current_semester')) {
                $table->integer('current_semester')->nullable()->after('user_id')->default(1);
            }
            if (!Schema::hasColumn('students', 'current_year')) {
                $table->integer('current_year')->nullable()->after('current_semester')->default(1);
            }
            if (!Schema::hasColumn('students', 'status')) {
                $table->string('status')->nullable()->after('current_year')->default('active');
            }
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['current_semester', 'current_year', 'status']);
        });
    }
};