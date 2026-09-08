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
        // 1. Job Assignments
        Schema::create('job_assignments', function (Blueprint $table) {
            $table->id();
            $table->string('job_order_no')->unique();
            $table->unsignedBigInteger('job_worker_id')->nullable();
            $table->string('job_worker_name');
            $table->string('process_name'); // Cutting, Stitching, Embroidery, Washing, Finishing, Packing
            $table->string('lot_number');
            $table->string('style_name');
            $table->date('issue_date');
            $table->date('due_date')->nullable();
            $table->integer('issued_qty');
            $table->integer('received_qty')->default(0);
            $table->integer('rejected_qty')->default(0);
            $table->decimal('rate_per_piece', 8, 2);
            $table->decimal('total_amount', 12, 2);
            $table->enum('status', ['Issued', 'In Progress', 'Partial Ready', 'Completed', 'Cancelled'])->default('Issued');
            $table->text('instructions')->nullable();
            $table->timestamps();

            $table->foreign('job_worker_id')->references('id')->on('job_workers')->onDelete('set null');
        });

        // 2. Job Assignment Line Items (breakdown by size & color)
        Schema::create('job_assignment_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('job_assignment_id');
            $table->string('size');
            $table->string('color');
            $table->integer('qty');
            $table->integer('ready_qty')->default(0);
            $table->integer('defect_qty')->default(0);
            $table->timestamps();

            $table->foreign('job_assignment_id')->references('id')->on('job_assignments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_assignment_items');
        Schema::dropIfExists('job_assignments');
    }
};
