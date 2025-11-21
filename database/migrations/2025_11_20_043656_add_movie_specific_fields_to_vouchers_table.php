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
        Schema::table('vouchers', function (Blueprint $table) {
            $table->foreignId('movie_id')->nullable()->after('code')->constrained()->onDelete('cascade')->comment('Áp dụng cho phim cụ thể, null = tất cả phim');
            $table->boolean('is_active')->default(true)->after('usage_count');
            $table->decimal('min_purchase_amount', 10, 2)->nullable()->after('is_active')->comment('Giá trị đơn hàng tối thiểu');
            $table->text('description')->nullable()->after('min_purchase_amount');
        });
    }

    public function down(): void
    {
        Schema::table('vouchers', function (Blueprint $table) {
            $table->dropForeign(['movie_id']);
            $table->dropColumn(['movie_id', 'is_active', 'min_purchase_amount', 'description']);
        });
    }
};
