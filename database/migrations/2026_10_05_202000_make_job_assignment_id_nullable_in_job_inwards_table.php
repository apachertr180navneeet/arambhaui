<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('job_inwards')) {
            // Drop foreign key if exists
            try {
                Schema::table('job_inwards', function (Blueprint $table) {
                    $table->dropForeign(['job_assignment_id']);
                });
            } catch (\Throwable $e) {
                try {
                    DB::statement("ALTER TABLE `job_inwards` DROP FOREIGN KEY `job_inwards_job_assignment_id_foreign`");
                } catch (\Throwable $e2) {}
            }

            // Make job_assignment_id, job_order_no, lot_number nullable so Inward can be completely independent
            try {
                DB::statement("ALTER TABLE `job_inwards` MODIFY COLUMN `job_assignment_id` BIGINT UNSIGNED NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE `job_inwards` MODIFY COLUMN `job_order_no` VARCHAR(255) NULL");
            } catch (\Throwable $e) {}

            try {
                DB::statement("ALTER TABLE `job_inwards` MODIFY COLUMN `job_worker_name` VARCHAR(255) NULL");
            } catch (\Throwable $e) {}

            // Optional foreign key that sets null on delete
            try {
                Schema::table('job_inwards', function (Blueprint $table) {
                    $table->foreign('job_assignment_id')->references('id')->on('job_assignments')->onDelete('set null');
                });
            } catch (\Throwable $e) {}
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op for safety
    }
};
