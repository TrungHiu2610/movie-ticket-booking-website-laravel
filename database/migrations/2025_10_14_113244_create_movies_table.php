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
        Schema::create('movies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description');
            $table->string('poster_url');
            $table->string('trailer_url');
            $table->integer('duration_minutes');
            $table->date('release_date');
            $table->string('age_rating'); // 'P', 'C13', 'C16', 'C18'
            $table->string('status'); // 'now_showing', 'coming_soon', 'ended'
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movies');
    }
};
