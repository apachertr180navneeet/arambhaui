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
        // 1. Sales Invoices
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->string('order_no')->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('company_name')->nullable();
            $table->string('gstin')->nullable();
            $table->date('invoice_date');
            $table->date('due_date');
            $table->decimal('subtotal', 12, 2);
            $table->decimal('discount_amount', 10, 2)->default(0.00);
            $table->string('voucher_code')->nullable();
            $table->decimal('taxable_amount', 12, 2);
            $table->decimal('cgst_rate', 5, 2)->default(2.50);
            $table->decimal('cgst_amount', 10, 2)->default(0.00);
            $table->decimal('sgst_rate', 5, 2)->default(2.50);
            $table->decimal('sgst_amount', 10, 2)->default(0.00);
            $table->decimal('igst_rate', 5, 2)->default(0.00);
            $table->decimal('igst_amount', 10, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2);
            $table->decimal('paid_amount', 12, 2)->default(0.00);
            $table->decimal('balance_due', 12, 2);
            $table->enum('status', ['Draft', 'Sent', 'Partial', 'Paid', 'Overdue', 'Cancelled'])->default('Sent');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // 2. Invoice Line Items
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('invoice_id');
            $table->string('item_description');
            $table->string('hsn_code')->default('6203');
            $table->integer('qty');
            $table->string('unit')->default('Pcs');
            $table->decimal('rate', 10, 2);
            $table->decimal('discount_percent', 5, 2)->default(0.00);
            $table->decimal('amount', 12, 2);
            $table->timestamps();

            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
    }
};
