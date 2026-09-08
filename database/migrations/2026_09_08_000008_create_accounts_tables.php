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
        // 1. Customer Payment Receipts
        Schema::create('customer_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('invoice_no')->nullable();
            $table->date('payment_date');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_mode', ['Bank Transfer (NEFT)', 'RTGS', 'UPI', 'Cheque', 'Cash'])->default('Bank Transfer (NEFT)');
            $table->string('reference_no')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // 2. Multi-Entity Financial / Stock Ledger Transactions
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('transaction_no')->unique();
            $table->date('transaction_date');
            $table->enum('ledger_type', ['Customer', 'Vendor', 'JobWorker', 'ItemStock']);
            $table->unsignedBigInteger('entity_id')->nullable();
            $table->string('entity_name');
            $table->string('reference_no')->nullable(); // Invoice No, GRN No, Payment Receipt No
            $table->string('transaction_type'); // Sales Invoice, Payment Received, Purchase GRN, Vendor Payment
            $table->decimal('debit', 12, 2)->default(0.00);
            $table->decimal('credit', 12, 2)->default(0.00);
            $table->decimal('running_balance', 12, 2)->default(0.00);
            $table->string('created_by')->default('Admin');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('customer_payments');
    }
};
