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
        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'total_thans')) {
                $table->integer('total_thans')->default(0)->after('issued_qty');
            }
            if (!Schema::hasColumn('job_assignments', 'than_details')) {
                $table->longText('than_details')->nullable()->after('total_than_meters');
            }
        });

        Schema::table('job_assignment_items', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignment_items', 'than_count')) {
                $table->integer('than_count')->default(0)->after('than_meters');
            }
            if (!Schema::hasColumn('job_assignment_items', 'than_details')) {
                $table->longText('than_details')->nullable()->after('than_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignments', 'total_thans')) {
                $table->dropColumn('total_thans');
            }
            if (Schema::hasColumn('job_assignments', 'than_details')) {
                $table->dropColumn('than_details');
            }
        });

        Schema::table('job_assignment_items', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignment_items', 'than_count')) {
                $table->dropColumn('than_count');
            }
            if (Schema::hasColumn('job_assignment_items', 'than_details')) {
                $table->dropColumn('than_details');
            }
        });
    }
};
