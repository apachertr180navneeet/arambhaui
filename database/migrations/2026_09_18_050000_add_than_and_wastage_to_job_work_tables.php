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
        // 1. Add total Than & Wastage summary fields to job_assignments
        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'total_than_meters')) {
                $table->decimal('total_than_meters', 12, 2)->default(0)->after('issued_qty');
            }
            if (!Schema::hasColumn('job_assignments', 'total_wastage_meters')) {
                $table->decimal('total_wastage_meters', 12, 2)->default(0)->after('total_than_meters');
            }
        });

        // 2. Add than meters, production pcs, wastage, rate & item details to job_assignment_items
        Schema::table('job_assignment_items', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignment_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('job_assignment_id');
            }
            if (!Schema::hasColumn('job_assignment_items', 'item_name')) {
                $table->string('item_name')->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('job_assignment_items', 'than_meters')) {
                $table->decimal('than_meters', 10, 2)->default(0)->after('item_name');
            }
            if (!Schema::hasColumn('job_assignment_items', 'production_pcs')) {
                $table->integer('production_pcs')->default(0)->after('than_meters');
            }
            if (!Schema::hasColumn('job_assignment_items', 'wastage_meters')) {
                $table->decimal('wastage_meters', 10, 2)->default(0)->after('production_pcs');
            }
            if (!Schema::hasColumn('job_assignment_items', 'avg_consumption')) {
                $table->decimal('avg_consumption', 10, 2)->default(0)->after('wastage_meters');
            }
            if (!Schema::hasColumn('job_assignment_items', 'rate_per_piece')) {
                $table->decimal('rate_per_piece', 10, 2)->default(0)->after('avg_consumption');
            }
            if (!Schema::hasColumn('job_assignment_items', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0)->after('rate_per_piece');
            }
            if (!Schema::hasColumn('job_assignment_items', 'remarks')) {
                $table->string('remarks')->nullable()->after('total_amount');
            }

            // Make size and color nullable if they weren't
            $table->string('size')->nullable()->change();
            $table->string('color')->nullable()->change();
            $table->integer('qty')->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            $table->dropColumn(['total_than_meters', 'total_wastage_meters']);
        });

        Schema::table('job_assignment_items', function (Blueprint $table) {
            $table->dropColumn([
                'item_id', 'item_name', 'than_meters', 'production_pcs',
                'wastage_meters', 'avg_consumption', 'rate_per_piece',
                'total_amount', 'remarks'
            ]);
        });
    }
};
