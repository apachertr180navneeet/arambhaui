<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('qr_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('qr_vouchers', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('payment_status');
            }
        });

        // Ensure status column allows 'Paid' (supports Active, Redeemed, Paid, Expired, Revoked)
        try {
            DB::statement("ALTER TABLE qr_vouchers MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Active'");
        } catch (\Throwable $e) {
            try {
                DB::statement("ALTER TABLE qr_vouchers MODIFY COLUMN status ENUM('Active', 'Redeemed', 'Paid', 'Expired', 'Revoked') NOT NULL DEFAULT 'Active'");
            } catch (\Throwable $e2) {
                // Silently continue
            }
        }
    }

    public function down(): void
    {
        Schema::table('qr_vouchers', function (Blueprint $table) {
            if (Schema::hasColumn('qr_vouchers', 'paid_at')) {
                $table->dropColumn('paid_at');
            }
        });
    }
};
