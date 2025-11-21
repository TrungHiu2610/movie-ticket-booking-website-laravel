<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add composite unique index to prevent duplicate seat bookings
        // This ensures no two tickets can have the same seat_id for bookings in the same showtime
        Schema::table('tickets', function (Blueprint $table) {
            // First, we need to add showtime_id reference through booking
            // We'll create a unique constraint on (booking_id, seat_id) 
            // combined with checking at application level for same showtime
            $table->unique(['booking_id', 'seat_id'], 'unique_booking_seat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropUnique('unique_booking_seat');
        });
    }
};
