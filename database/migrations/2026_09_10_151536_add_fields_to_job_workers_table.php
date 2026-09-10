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
        Schema::table('job_workers', function (Blueprint $table) {
            if (!Schema::hasColumn('job_workers', 'contact_person')) {
                $table->string('contact_person', 255)->nullable()->after('name');
            }
            if (!Schema::hasColumn('job_workers', 'email')) {
                $table->string('email', 255)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('job_workers', 'city')) {
                $table->string('city', 100)->nullable()->after('address');
            }
            if (!Schema::hasColumn('job_workers', 'state')) {
                $table->string('state', 100)->nullable()->after('city');
            }
            if (!Schema::hasColumn('job_workers', 'pincode')) {
                $table->string('pincode', 20)->nullable()->after('state');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_workers', function (Blueprint $table) {
            $table->dropColumn(['contact_person', 'email', 'city', 'state', 'pincode']);
        });
    }
};
