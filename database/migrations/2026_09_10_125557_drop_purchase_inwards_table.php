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
        Schema::dropIfExists('purchase_inwards');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('purchase_inwards', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete();
            $table->foreignId('vendor_id')->nullable()->constrained('vendors')->nullOnDelete();
            $table->date('inward_date');
            $table->string('supplier_invoice_no')->nullable();
            $table->string('warehouse')->default('Main Raw Material Store - Unit 1');
            $table->string('received_by')->nullable();
            $table->decimal('received_qty', 12, 2)->default(0);
            $table->decimal('accepted_qty', 12, 2)->default(0);
            $table->decimal('rejected_qty', 12, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
        });
    }
};
