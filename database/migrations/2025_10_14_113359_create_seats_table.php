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
        Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('theater_id')->constrained()->onDelete('cascade');
            $table->foreignId('seat_type_id')->constrained('seat_types');
            $table->char('row_char', 2);
            $table->integer('column_number');
            $table->unique(['theater_id', 'row_char', 'column_number']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seats');
    }
};
