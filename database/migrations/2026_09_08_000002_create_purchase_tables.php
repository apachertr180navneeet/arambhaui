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
        // 1. Purchase Orders (PO)
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name');
            $table->date('po_date');
            $table->date('expected_delivery_date')->nullable();
            $table->string('warehouse_location')->default('Main Store - Unit 1');
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_total', 10, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->enum('payment_status', ['Pending', 'Partially Paid', 'Paid'])->default('Pending');
            $table->enum('status', ['Draft', 'Approved', 'Partially Received', 'Received', 'Cancelled'])->default('Approved');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });

        // 2. Purchase Order Line Items
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('purchase_order_id');
            $table->unsignedBigInteger('item_id')->nullable();
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->decimal('ordered_qty', 10, 2);
            $table->decimal('received_qty', 10, 2)->default(0.00);
            $table->string('unit')->default('Meters');
            $table->decimal('rate', 10, 2);
            $table->decimal('tax_percent', 5, 2)->default(5.00);
            $table->decimal('tax_amount', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
        });

        // 3. Purchase Inward / Goods Receipt Notes (GRN)
        Schema::create('purchase_inwards', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->unsignedBigInteger('purchase_order_id')->nullable();
            $table->string('po_number')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name');
            $table->date('received_date');
            $table->string('warehouse_location')->default('Main Store - Unit 1');
            $table->string('item_name');
            $table->string('item_code')->nullable();
            $table->decimal('ordered_qty', 10, 2)->default(0.00);
            $table->decimal('received_qty', 10, 2);
            $table->decimal('accepted_qty', 10, 2);
            $table->decimal('rejected_qty', 10, 2)->default(0.00);
            $table->string('lot_number')->nullable();
            $table->string('supplier_invoice_no')->nullable();
            $table->string('inspected_by')->nullable();
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Approved');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('set null');
            $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_inwards');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
    }
};
