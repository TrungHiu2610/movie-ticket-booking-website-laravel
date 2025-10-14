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
        Schema::create('surcharges', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // VD: "Phụ thu cuối tuần", "Phụ thu phim 3D"
            $table->decimal('amount', 10, 2); // Số tiền phụ thu, có thể là số dương
            $table->string('type'); // 'DAY_OF_WEEK', 'SPECIFIC_DATE', 'SCREEN_TYPE'
            $table->string('apply_condition'); // "6,7" (cho T7,CN), "2025-04-30", "3D"
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surcharges');
    }
};
