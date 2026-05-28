<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('teachers', 'user_id')) {

            Schema::table('teachers', function (Blueprint $table) {

                $table->unsignedBigInteger('user_id')
                      ->nullable()
                      ->after('id');

            });

        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('teachers', 'user_id')) {

            Schema::table('teachers', function (Blueprint $table) {

                $table->dropColumn('user_id');

            });

        }
    }
};