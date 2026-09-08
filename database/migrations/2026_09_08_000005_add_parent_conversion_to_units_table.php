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
        Schema::table('units', function (Blueprint $table) {
            if (!Schema::hasColumn('units', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->constrained('units')->nullOnDelete()->after('name');
            }
            if (!Schema::hasColumn('units', 'conversion_factor')) {
                $table->decimal('conversion_factor', 15, 4)->nullable()->after('parent_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('units', function (Blueprint $table) {
            if (Schema::hasColumn('units', 'parent_id')) {
                $table->dropForeign(['parent_id']);
                $table->dropColumn('parent_id');
            }
            if (Schema::hasColumn('units', 'conversion_factor')) {
                $table->dropColumn('conversion_factor');
            }
        });
    }
};
