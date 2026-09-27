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
        Schema::table('job_assignment_items', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignment_items', 'raw_item_id')) {
                $table->unsignedBigInteger('raw_item_id')->nullable()->after('job_assignment_id');
            }
            if (!Schema::hasColumn('job_assignment_items', 'raw_item_name')) {
                $table->string('raw_item_name')->nullable()->after('raw_item_id');
            }
            if (!Schema::hasColumn('job_assignment_items', 'finished_item_id')) {
                $table->unsignedBigInteger('finished_item_id')->nullable()->after('raw_item_name');
            }
            if (!Schema::hasColumn('job_assignment_items', 'finished_item_name')) {
                $table->string('finished_item_name')->nullable()->after('finished_item_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignment_items', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignment_items', 'raw_item_id')) {
                $table->dropColumn('raw_item_id');
            }
            if (Schema::hasColumn('job_assignment_items', 'raw_item_name')) {
                $table->dropColumn('raw_item_name');
            }
            if (Schema::hasColumn('job_assignment_items', 'finished_item_id')) {
                $table->dropColumn('finished_item_id');
            }
            if (Schema::hasColumn('job_assignment_items', 'finished_item_name')) {
                $table->dropColumn('finished_item_name');
            }
        });
    }
};
