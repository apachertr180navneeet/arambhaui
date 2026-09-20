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
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'challan_no')) {
                $table->string('challan_no')->nullable()->after('po_number');
            }
            if (!Schema::hasColumn('purchase_orders', 'total_thans')) {
                $table->integer('total_thans')->default(0)->after('grand_total');
            }
            if (!Schema::hasColumn('purchase_orders', 'than_details')) {
                $table->longText('than_details')->nullable()->after('total_thans');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_order_items', 'than_count')) {
                $table->integer('than_count')->default(0)->after('ordered_qty');
            }
            if (!Schema::hasColumn('purchase_order_items', 'than_details')) {
                $table->longText('than_details')->nullable()->after('than_count');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'challan_no')) {
                $table->dropColumn('challan_no');
            }
            if (Schema::hasColumn('purchase_orders', 'total_thans')) {
                $table->dropColumn('total_thans');
            }
            if (Schema::hasColumn('purchase_orders', 'than_details')) {
                $table->dropColumn('than_details');
            }
        });

        Schema::table('purchase_order_items', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_order_items', 'than_count')) {
                $table->dropColumn('than_count');
            }
            if (Schema::hasColumn('purchase_order_items', 'than_details')) {
                $table->dropColumn('than_details');
            }
        });
    }
};
