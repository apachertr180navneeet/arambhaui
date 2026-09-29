<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('units')) {
            DB::table('units')
                ->whereIn(DB::raw('UPPER(code)'), ['PCS', 'PC'])
                ->orWhereIn(DB::raw('UPPER(name)'), ['PCS', 'PIECES'])
                ->update(['decimal_places' => 0]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
