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
        Schema::table('job_assignments', function (Blueprint $table) {
            if (!Schema::hasColumn('job_assignments', 'photos')) {
                $table->longText('photos')->nullable()->after('instructions');
            }
            if (!Schema::hasColumn('job_assignments', 'sample_photo')) {
                $table->string('sample_photo', 500)->nullable()->after('instructions');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('job_assignments', function (Blueprint $table) {
            if (Schema::hasColumn('job_assignments', 'photos')) {
                $table->dropColumn('photos');
            }
            if (Schema::hasColumn('job_assignments', 'sample_photo')) {
                $table->dropColumn('sample_photo');
            }
        });
    }
};
