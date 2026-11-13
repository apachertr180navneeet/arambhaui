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
        // 1. Job Assignments: Add maintain_stock toggle (dynamic vs dummy job work)
        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'maintain_stock')) {
                $table->boolean('maintain_stock')->default(true)->after('status')
                      ->comment('True = Dynamic Job Work (auto-deduct stock); False = Dummy Job Work (skip stock maintenance)');
            }
        });

        // 2. Dispatch Challans (Sales Entry): Add bilty_no, transport, totals, thans
        Schema::table('dispatch_challans', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatch_challans', 'bilty_no')) {
                $table->string('bilty_no')->nullable()->after('lr_number');
            }
            if (!Schema::hasColumn('dispatch_challans', 'total_thans')) {
                $table->integer('total_thans')->default(0)->after('total_qty');
            }
            if (!Schema::hasColumn('dispatch_challans', 'subtotal_amount')) {
                $table->decimal('subtotal_amount', 15, 2)->default(0)->after('total_thans');
            }
            if (!Schema::hasColumn('dispatch_challans', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('subtotal_amount');
            }
            if (!Schema::hasColumn('dispatch_challans', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('dispatch_challans', 'remarks')) {
                $table->text('remarks')->nullable()->after('total_amount');
            }
        });

        // 3. Dispatch Items (Sales Items): Add rate, tax, amounts, than details
        Schema::table('dispatch_items', function (Blueprint $table) {
            if (!Schema::hasColumn('dispatch_items', 'rate')) {
                $table->decimal('rate', 10, 2)->default(0)->after('qty');
            }
            if (!Schema::hasColumn('dispatch_items', 'tax_percent')) {
                $table->decimal('tax_percent', 5, 2)->default(0)->after('rate');
            }
            if (!Schema::hasColumn('dispatch_items', 'tax_amount')) {
                $table->decimal('tax_amount', 15, 2)->default(0)->after('tax_percent');
            }
            if (!Schema::hasColumn('dispatch_items', 'total_amount')) {
                $table->decimal('total_amount', 15, 2)->default(0)->after('tax_amount');
            }
            if (!Schema::hasColumn('dispatch_items', 'than_count')) {
                $table->integer('than_count')->default(0)->after('total_amount');
            }
            if (!Schema::hasColumn('dispatch_items', 'thans_data')) {
                $table->text('thans_data')->nullable()->after('than_count');
            }
            if (!Schema::hasColumn('dispatch_items', 'item_category')) {
                $table->string('item_category')->nullable()->after('style_name')
                      ->comment('Raw Fabric or Finished Goods');
            }
        });

        // 4. Purchase Orders: Ensure transport_name, bilty_no, total_thans exist
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_orders', 'transport_name')) {
                $table->string('transport_name')->nullable()->after('vendor_name');
            }
            if (!Schema::hasColumn('purchase_orders', 'bilty_no')) {
                $table->string('bilty_no')->nullable()->after('transport_name');
            }
            if (!Schema::hasColumn('purchase_orders', 'total_thans')) {
                $table->integer('total_thans')->default(0)->after('total_qty');
            }
        });

        // 5. QR Vouchers: Add address, gstin, pincode metadata
        Schema::table('qr_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('qr_vouchers', 'address')) {
                $table->text('address')->nullable()->after('customer_phone');
            }
            if (!Schema::hasColumn('qr_vouchers', 'pincode')) {
                $table->string('pincode', 20)->nullable()->after('address');
            }
            if (!Schema::hasColumn('qr_vouchers', 'gstin')) {
                $table->string('gstin', 50)->nullable()->after('pincode');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignments', 'maintain_stock')) {
                $table->dropColumn('maintain_stock');
            }
        });

        Schema::table('dispatch_challans', function (Blueprint $table) {
            $cols = ['bilty_no', 'total_thans', 'subtotal_amount', 'tax_amount', 'total_amount', 'remarks'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('dispatch_challans', $c)) $table->dropColumn($c);
            }
        });

        Schema::table('dispatch_items', function (Blueprint $table) {
            $cols = ['rate', 'tax_percent', 'tax_amount', 'total_amount', 'than_count', 'thans_data', 'item_category'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('dispatch_items', $c)) $table->dropColumn($c);
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $cols = ['transport_name', 'bilty_no', 'total_thans'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('purchase_orders', $c)) $table->dropColumn($c);
            }
        });

        Schema::table('qr_vouchers', function (Blueprint $table) {
            $cols = ['address', 'pincode', 'gstin'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('qr_vouchers', $c)) $table->dropColumn($c);
            }
        });
    }
};

