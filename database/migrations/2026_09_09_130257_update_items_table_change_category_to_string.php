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
        Schema::table('items', function (Blueprint $table) {
            $table->string('category', 100)->default('Fabric')->change();
            if (!Schema::hasColumn('items', 'type')) {
                $table->string('type', 100)->nullable()->after('name');
            }
            if (!Schema::hasColumn('items', 'brand')) {
                $table->string('brand', 100)->nullable()->after('category');
            }
            if (!Schema::hasColumn('items', 'fabric')) {
                $table->string('fabric', 150)->nullable()->after('brand');
            }
            if (!Schema::hasColumn('items', 'color')) {
                $table->string('color', 100)->nullable()->after('fabric');
            }
            if (!Schema::hasColumn('items', 'size')) {
                $table->string('size', 50)->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['type', 'brand', 'fabric', 'color', 'size']);
        });
    }
};
