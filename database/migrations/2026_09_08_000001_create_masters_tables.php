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
        // 1. Customers Table
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('gstin')->nullable();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->default('Maharashtra');
            $table->decimal('credit_limit', 12, 2)->default(500000.00);
            $table->decimal('outstanding', 12, 2)->default(0.00);
            $table->string('payment_terms')->default('Net 30 Days');
            $table->enum('status', ['Active', 'Inactive', 'Blocked'])->default('Active');
            $table->timestamps();
        });

        // 2. Vendors Table
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('contact_person')->nullable();
            $table->string('phone');
            $table->string('email')->nullable();
            $table->string('gstin')->nullable();
            $table->text('address')->nullable();
            $table->string('category')->default('Fabric Supplier');
            $table->integer('credit_days')->default(45);
            $table->decimal('outstanding', 12, 2)->default(0.00);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // 3. Job Workers Table
        Schema::create('job_workers', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('phone');
            $table->string('skill_type'); // Cutting, Stitching, Embroidery, Washing, Finishing, Packing
            $table->decimal('rate_per_piece', 8, 2)->default(15.00);
            $table->integer('daily_capacity')->default(250);
            $table->text('address')->nullable();
            $table->decimal('outstanding', 12, 2)->default(0.00);
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // 4. Items Table
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->enum('category', ['Fabric', 'Trims', 'Accessories', 'Packing', 'Finished Goods']);
            $table->string('unit')->default('Meters'); // Meters, Kg, Pcs, Cones, Gross, Cartons
            $table->string('hsn_code')->nullable();
            $table->decimal('unit_cost', 10, 2)->default(0.00);
            $table->decimal('current_stock', 10, 2)->default(0.00);
            $table->decimal('min_stock', 10, 2)->default(100.00);
            $table->string('location')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // 5. Sizes Table
        Schema::create('sizes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // 6. Colors Table
        Schema::create('colors', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('hex_code')->default('#000000');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('colors');
        Schema::dropIfExists('sizes');
        Schema::dropIfExists('items');
        Schema::dropIfExists('job_workers');
        Schema::dropIfExists('vendors');
        Schema::dropIfExists('customers');
    }
};
