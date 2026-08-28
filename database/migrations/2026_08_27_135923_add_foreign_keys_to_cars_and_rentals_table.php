<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->foreignId('location_id')->after('is_available')->constrained()->onDelete('cascade');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->foreignId('user_id')->after('id')->constrained()->onDelete('cascade');
            $table->foreignId('car_id')->after('user_id')->constrained()->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn('location_id');
        });

        Schema::table('rentals', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['car_id']);
            $table->dropColumn(['user_id', 'car_id']);
        });
    }
};
