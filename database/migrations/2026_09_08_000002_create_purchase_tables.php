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
        // 1. Purchase Orders
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name');
            $table->date('po_date');
            $table->date('expected_date')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('tax_amount', 12, 2)->default(0.00);
            $table->decimal('grand_total', 12, 2)->default(0.00);
            $table->enum('status', ['Draft', 'Approved', 'Partially Received', 'Completed', 'Cancelled'])->default('Draft');
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
            $table->decimal('qty', 10, 2);
            $table->string('unit')->default('Meters');
            $table->decimal('unit_price', 10, 2);
            $table->decimal('tax_percent', 5, 2)->default(5.00);
            $table->decimal('total_amount', 12, 2);
            $table->timestamps();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
        });

        // 3. Purchase Inwards / GRN (Goods Receipt Note)
        Schema::create('purchase_inwards', function (Blueprint $table) {
            $table->id();
            $table->string('grn_number')->unique();
            $table->string('po_number')->nullable();
            $table->unsignedBigInteger('vendor_id')->nullable();
            $table->string('vendor_name');
            $table->string('lot_number')->unique();
            $table->string('item_name');
            $table->decimal('ordered_qty', 10, 2)->default(0.00);
            $table->decimal('received_qty', 10, 2);
            $table->decimal('rejected_qty', 10, 2)->default(0.00);
            $table->string('unit')->default('Meters');
            $table->date('inward_date');
            $table->string('warehouse_location')->default('Central Warehouse - Zone A');
            $table->string('vehicle_number')->nullable();
            $table->string('vendor_invoice_no')->nullable();
            $table->enum('status', ['Accepted', 'Partial Acceptance', 'Under Inspection', 'Rejected'])->default('Accepted');
            $table->text('remarks')->nullable();
            $table->timestamps();

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
