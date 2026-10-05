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
            if (!Schema::hasColumn('qr_vouchers', 'recipient_address')) {
                $table->text('recipient_address')->nullable()->after('recipient_name');
            }
            if (!Schema::hasColumn('qr_vouchers', 'recipient_pincode')) {
                $table->string('recipient_pincode', 20)->nullable()->after('recipient_address');
            }
            if (!Schema::hasColumn('qr_vouchers', 'recipient_city')) {
                $table->string('recipient_city', 100)->nullable()->after('recipient_pincode');
            }
            if (!Schema::hasColumn('qr_vouchers', 'recipient_state')) {
                $table->string('recipient_state', 100)->nullable()->after('recipient_city');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('qr_vouchers', function (Blueprint $table) {
            $cols = ['recipient_address', 'recipient_pincode', 'recipient_city', 'recipient_state'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('qr_vouchers', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
