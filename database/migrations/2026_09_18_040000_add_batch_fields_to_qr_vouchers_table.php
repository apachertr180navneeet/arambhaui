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
        Schema::table('qr_vouchers', function (Blueprint $table) {
            if (!Schema::hasColumn('qr_vouchers', 'batch_name')) {
                $table->string('batch_name')->nullable()->after('customer_name');
            }
            if (!Schema::hasColumn('qr_vouchers', 'qr_date')) {
                $table->date('qr_date')->nullable()->after('batch_name');
            }
            if (!Schema::hasColumn('qr_vouchers', 'amount')) {
                $table->decimal('amount', 10, 2)->nullable()->after('qr_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_vouchers', function (Blueprint $table) {
            $table->dropColumn(['batch_name', 'qr_date', 'amount']);
        });
    }
};
