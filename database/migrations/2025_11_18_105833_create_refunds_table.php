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
        Schema::create('refunds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
            $table->foreignId('processed_by')->nullable()->constrained('users')->comment('Staff xử lý hoàn tiền');
            $table->string('refund_code')->unique()->comment('Mã giao dịch hoàn tiền');
            $table->decimal('original_amount', 10, 2)->comment('Số tiền gốc');
            $table->decimal('refund_fee', 10, 2)->default(0)->comment('Phí hoàn tiền');
            $table->decimal('refund_amount', 10, 2)->comment('Số tiền được hoàn');
            $table->text('reason')->nullable()->comment('Lý do hoàn tiền');
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();
            $table->text('staff_notes')->nullable()->comment('Ghi chú của nhân viên');
            $table->timestamps();
        });

        // Add refund status to bookings table
        Schema::table('bookings', function (Blueprint $table) {
            $table->enum('refund_status', ['none', 'requested', 'refunded'])->default('none')->after('status');
            $table->timestamp('refunded_at')->nullable()->after('refund_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['refund_status', 'refunded_at']);
        });

        Schema::dropIfExists('refunds');
    }
};
