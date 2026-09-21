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
        Schema::table('dispatch_items', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatch_items', 'item_id')) {
                $table->unsignedBigInteger('item_id')->nullable()->after('dispatch_challan_id');
                $table->foreign('item_id')->references('id')->on('items')->onDelete('set null');
            }
            if (!Schema::hasColumn('dispatch_items', 'batch_no')) {
                $table->string('batch_no', 100)->nullable()->after('item_id');
            }
            if (!Schema::hasColumn('dispatch_items', 'unit')) {
                $table->string('unit', 50)->default('Pcs')->after('qty');
            }
            if (!Schema::hasColumn('dispatch_items', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->default(0.00)->after('unit');
            }
            if (!Schema::hasColumn('dispatch_items', 'total_amount')) {
                $table->decimal('total_amount', 12, 2)->default(0.00)->after('unit_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dispatch_items', function (Blueprint $table) {
            if (Schema::hasColumn('dispatch_items', 'item_id')) {
                $table->dropForeign(['item_id']);
                $table->dropColumn('item_id');
            }
            $table->dropColumn(['batch_no', 'unit', 'unit_price', 'total_amount']);
        });
    }
};
