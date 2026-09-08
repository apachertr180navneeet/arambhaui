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
        // 1. Production / Customer Orders
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no')->unique();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->string('style_name');
            $table->string('item_description')->nullable();
            $table->integer('order_qty');
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->decimal('total_amount', 12, 2)->default(0.00);
            $table->date('order_date');
            $table->date('delivery_date');
            $table->string('current_stage')->default('Cutting'); // Cutting, Stitching, Embroidery, Washing, Finishing, QC, Ready, Dispatched
            $table->integer('progress_percent')->default(10);
            $table->enum('status', ['Scheduled', 'In Production', 'Quality Check', 'Ready for Dispatch', 'Completed', 'On Hold'])->default('Scheduled');
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // 2. Quality Checks (QC Inspection)
        Schema::create('quality_checks', function (Blueprint $table) {
            $table->id();
            $table->string('qc_batch_no')->unique();
            $table->string('order_no');
            $table->string('lot_number');
            $table->string('style_name');
            $table->date('inspection_date');
            $table->string('inspector_name');
            $table->integer('total_inspected');
            $table->integer('passed_qty');
            $table->integer('minor_defects')->default(0);
            $table->integer('major_defects')->default(0);
            $table->decimal('defect_percent', 5, 2)->default(0.00);
            $table->enum('status', ['Passed', 'Rework Required', 'Rejected'])->default('Passed');
            $table->text('defect_breakdown')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 3. Lot Trackings & Lineage
        Schema::create('lot_trackings', function (Blueprint $table) {
            $table->id();
            $table->string('lot_number')->unique();
            $table->string('order_no')->nullable();
            $table->string('style_name');
            $table->string('fabric_lot_ref')->nullable();
            $table->integer('initial_qty');
            $table->integer('current_qty');
            $table->string('current_stage');
            $table->string('current_location');
            $table->string('assigned_worker')->nullable();
            $table->string('qr_code_hash')->nullable();
            $table->enum('status', ['Active WIP', 'QC Passed', 'Packed', 'Dispatched'])->default('Active WIP');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lot_trackings');
        Schema::dropIfExists('quality_checks');
        Schema::dropIfExists('production_orders');
    }
};
