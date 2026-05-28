<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // CHECK TABLE EXISTS
        if (!Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {

            // CURRENT SEMESTER
            if (!Schema::hasColumn('students', 'current_semester')) {

                $table->integer('current_semester')
                    ->nullable()
                    ->default(1);

            }

            // CURRENT YEAR
            if (!Schema::hasColumn('students', 'current_year')) {

                $table->integer('current_year')
                    ->nullable()
                    ->default(1);

            }

            // STATUS
            if (!Schema::hasColumn('students', 'status')) {

                $table->string('status')
                    ->nullable()
                    ->default('active');

            }

        });
    }

    public function down()
    {
        if (!Schema::hasTable('students')) {
            return;
        }

        Schema::table('students', function (Blueprint $table) {

            if (Schema::hasColumn('students', 'current_semester')) {
                $table->dropColumn('current_semester');
            }

            if (Schema::hasColumn('students', 'current_year')) {
                $table->dropColumn('current_year');
            }

            if (Schema::hasColumn('students', 'status')) {
                $table->dropColumn('status');
            }

        });
    }
};