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
        // 1. Dispatch Challans
        Schema::create('dispatch_challans', function (Blueprint $table) {
            $table->id();
            $table->string('challan_no')->unique();
            $table->string('order_no');
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->string('customer_name');
            $table->date('dispatch_date');
            $table->string('transporter_name')->default('SafeExpress Logistics');
            $table->string('lr_number')->nullable();
            $table->string('vehicle_number')->nullable();
            $table->string('driver_contact')->nullable();
            $table->string('destination_city')->nullable();
            $table->integer('total_cartons')->default(1);
            $table->integer('total_qty');
            $table->decimal('gross_weight_kg', 8, 2)->default(0.00);
            $table->enum('status', ['Prepared', 'In Transit', 'Delivered', 'Cancelled'])->default('Prepared');
            $table->text('remarks')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
        });

        // 2. Dispatch Line Items
        Schema::create('dispatch_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dispatch_challan_id');
            $table->string('style_name');
            $table->string('size');
            $table->string('color');
            $table->integer('qty');
            $table->string('carton_barcode')->nullable();
            $table->timestamps();

            $table->foreign('dispatch_challan_id')->references('id')->on('dispatch_challans')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
        Schema::dropIfExists('dispatch_challans');
    }
};
