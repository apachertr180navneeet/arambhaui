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
        // 1. Vendor Payments (Outgoing Payments to Suppliers)
        if (!Schema::hasTable('vendor_payments')) {
            Schema::create('vendor_payments', function (Blueprint $table) {
                $table->id();
                $table->string('payment_no')->unique();
                $table->unsignedBigInteger('vendor_id')->nullable();
                $table->string('vendor_name');
                $table->date('payment_date');
                $table->decimal('amount', 12, 2);
                $table->string('payment_mode')->default('Bank Transfer (NEFT)');
                $table->string('reference_no')->nullable(); // UTR / Cheque No / Transaction ID
                $table->string('bank_name')->nullable();
                $table->text('notes')->nullable();
                $table->string('created_by')->default('Admin');
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
            });
        }

        // 2. Payment Allocations (Mapping Payments to Purchase Bills / Orders)
        if (!Schema::hasTable('purchase_payment_allocations')) {
            Schema::create('purchase_payment_allocations', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('vendor_payment_id');
                $table->unsignedBigInteger('purchase_order_id');
                $table->decimal('allocated_amount', 12, 2);
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('vendor_payment_id')->references('id')->on('vendor_payments')->onDelete('cascade');
                $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
                $table->unique(['vendor_payment_id', 'purchase_order_id'], 'v_pay_po_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_payment_allocations');
        Schema::dropIfExists('vendor_payments');
    }
};
