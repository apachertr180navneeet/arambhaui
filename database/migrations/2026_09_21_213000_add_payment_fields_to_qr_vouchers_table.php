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
            if (!Schema::hasColumn('qr_vouchers', 'recipient_upi_id')) {
                $table->string('recipient_upi_id')->nullable()->after('redeemed_invoice_no');
            }
            if (!Schema::hasColumn('qr_vouchers', 'recipient_name')) {
                $table->string('recipient_name')->nullable()->after('recipient_upi_id');
            }
            if (!Schema::hasColumn('qr_vouchers', 'original_bill')) {
                $table->decimal('original_bill', 12, 2)->nullable()->after('recipient_name');
            }
            if (!Schema::hasColumn('qr_vouchers', 'discount_claimed')) {
                $table->decimal('discount_claimed', 12, 2)->nullable()->after('original_bill');
            }
            if (!Schema::hasColumn('qr_vouchers', 'final_payable')) {
                $table->decimal('final_payable', 12, 2)->nullable()->after('discount_claimed');
            }
            if (!Schema::hasColumn('qr_vouchers', 'payment_status')) {
                $table->string('payment_status', 50)->default('Pending')->after('final_payable');
            }
            if (!Schema::hasColumn('qr_vouchers', 'payment_method')) {
                $table->string('payment_method', 50)->default('UPI')->after('payment_status');
            }
            if (!Schema::hasColumn('qr_vouchers', 'upi_txn_ref')) {
                $table->string('upi_txn_ref')->nullable()->after('payment_method');
            }
            if (!Schema::hasColumn('qr_vouchers', 'recipient_qr_image')) {
                $table->text('recipient_qr_image')->nullable()->after('upi_txn_ref');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_vouchers', function (Blueprint $table) {
            $cols = [
                'recipient_upi_id',
                'recipient_name',
                'original_bill',
                'discount_claimed',
                'final_payable',
                'payment_status',
                'payment_method',
                'upi_txn_ref',
                'recipient_qr_image'
            ];
            foreach ($cols as $col) {
                if (Schema::hasColumn('qr_vouchers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
