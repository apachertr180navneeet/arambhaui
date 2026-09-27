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
        Schema::table('job_inwards', function (Blueprint $table) {
            if (!Schema::hasColumn('job_inwards', 'total_thans')) {
                $table->integer('total_thans')->default(0)->after('defect_qty');
            }
            if (!Schema::hasColumn('job_inwards', 'total_meters')) {
                $table->decimal('total_meters', 12, 2)->default(0)->after('total_thans');
            }
            if (!Schema::hasColumn('job_inwards', 'than_details')) {
                $table->longText('than_details')->nullable()->after('total_meters');
            }
            if (!Schema::hasColumn('job_inwards', 'items_data')) {
                $table->longText('items_data')->nullable()->after('than_details');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_inwards', function (Blueprint $table) {
            if (Schema::hasColumn('job_inwards', 'total_thans')) {
                $table->dropColumn('total_thans');
            }
            if (Schema::hasColumn('job_inwards', 'total_meters')) {
                $table->dropColumn('total_meters');
            }
            if (Schema::hasColumn('job_inwards', 'than_details')) {
                $table->dropColumn('than_details');
            }
            if (Schema::hasColumn('job_inwards', 'items_data')) {
                $table->dropColumn('items_data');
            }
        });
    }
};
