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
        if (!Schema::hasTable('job_inwards')) {
            Schema::create('job_inwards', function (Blueprint $table) {
                $table->id();
                $table->string('inward_number')->unique();
                $table->unsignedBigInteger('job_assignment_id');
                $table->string('job_order_no');
                $table->string('lot_number');
                $table->unsignedBigInteger('job_worker_id')->nullable();
                $table->string('job_worker_name');
                $table->string('process_name')->nullable();
                $table->string('style_name')->nullable();
                $table->date('inward_date');
                $table->string('challan_no')->nullable(); // Worker delivery challan
                $table->integer('received_qty')->default(0); // Good finished pieces
                $table->integer('defect_qty')->default(0); // Defect / rejected pieces
                $table->decimal('wastage_returned_meters', 10, 2)->default(0); // Wastage meters returned
                $table->decimal('rate_per_piece', 10, 2)->default(0);
                $table->decimal('total_amount', 12, 2)->default(0);
                $table->string('qc_status')->default('Passed QC');
                $table->string('storage_location')->default('Finished Goods Stock');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->softDeletes();

                $table->foreign('job_assignment_id')->references('id')->on('job_assignments')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_inwards');
    }
};
