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
        Schema::create('qr_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('voucher_code')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('title')->default('Promotional Seasonal Discount');
            $table->enum('discount_type', ['Percentage', 'Flat'])->default('Percentage');
            $table->decimal('discount_percent', 5, 2)->default(10.00);
            $table->decimal('discount_amount', 10, 2)->nullable();
            $table->decimal('max_discount_cap', 10, 2)->default(5000.00);
            $table->decimal('min_order_value', 10, 2)->default(25000.00);
            $table->date('valid_from');
            $table->date('valid_until');
            $table->boolean('is_redeemed')->default(false);
            $table->timestamp('redeemed_at')->nullable();
            $table->string('redeemed_invoice_no')->nullable();
            $table->text('qr_payload')->nullable();
            $table->enum('status', ['Active', 'Redeemed', 'Expired', 'Revoked'])->default('Active');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('qr_vouchers');
    }
};
