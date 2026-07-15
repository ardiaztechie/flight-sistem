<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_passengers', function (Blueprint $table) {
            // Buat nullable agar tidak error saat seat_id tidak tersedia
            $table->foreignId('flight_seat_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_passengers', function (Blueprint $table) {
            $table->foreignId('flight_seat_id')->nullable(false)->change();
        });
    }
};
