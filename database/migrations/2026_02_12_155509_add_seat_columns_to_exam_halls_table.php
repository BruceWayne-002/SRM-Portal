<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            if (!Schema::hasColumn('exam_halls', 'seat_layout')) {
                $table->json('seat_layout')->nullable()->after('exam_type');
            }
            if (!Schema::hasColumn('exam_halls', 'allocated_seats')) {
                $table->json('allocated_seats')->nullable()->after('seat_layout');
            }
            if (!Schema::hasColumn('exam_halls', 'available_seats')) {
                $table->integer('available_seats')->nullable()->after('capacity');
            }
        });
    }

    public function down()
    {
        Schema::table('exam_halls', function (Blueprint $table) {
            $table->dropColumn(['seat_layout', 'allocated_seats', 'available_seats']);
        });
    }
};