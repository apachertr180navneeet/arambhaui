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
        // 1. Ensure email is nullable in Customer, Vendor, and JobWorker tables
        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'email')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('email', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('vendors') && Schema::hasColumn('vendors', 'email')) {
            Schema::table('vendors', function (Blueprint $table) {
                $table->string('email', 255)->nullable()->change();
            });
        }

        if (Schema::hasTable('job_workers') && Schema::hasColumn('job_workers', 'email')) {
            Schema::table('job_workers', function (Blueprint $table) {
                $table->string('email', 255)->nullable()->change();
            });
        }

        // 2. Add stock quantity tracking columns to transactions table if not present
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'qty_in')) {
                    $table->decimal('qty_in', 12, 2)->default(0.00)->after('credit');
                }
                if (!Schema::hasColumn('transactions', 'qty_out')) {
                    $table->decimal('qty_out', 12, 2)->default(0.00)->after('qty_in');
                }
                if (!Schema::hasColumn('transactions', 'unit')) {
                    $table->string('unit', 50)->nullable()->after('qty_out');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (Schema::hasColumn('transactions', 'qty_in')) {
                    $table->dropColumn('qty_in');
                }
                if (Schema::hasColumn('transactions', 'qty_out')) {
                    $table->dropColumn('qty_out');
                }
                if (Schema::hasColumn('transactions', 'unit')) {
                    $table->dropColumn('unit');
                }
            });
        }
    }
};

