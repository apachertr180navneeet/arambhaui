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
        Schema::create('units', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // e.g. MTR, PCS, KG, DZN, ROL, BOX, YRD, CONE, GRS
            $table->string('name', 100);          // e.g. Meters, Pieces, Kilograms, Dozens, Rolls, Boxes
            $table->string('symbol', 20)->nullable(); // e.g. m, pcs, kg, dz, roll, box
            $table->integer('decimal_places')->default(2); // 0, 2, 3
            $table->text('description')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });

        // Insert initial standard garment industry units
        DB::table('units')->insert([
            ['code' => 'MTR', 'name' => 'Meters', 'symbol' => 'm', 'decimal_places' => 2, 'description' => 'Fabric linear length measurement', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'PCS', 'name' => 'Pieces', 'symbol' => 'pcs', 'decimal_places' => 0, 'description' => 'Individual garment or finished item count', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'KG', 'name' => 'Kilograms', 'symbol' => 'kg', 'decimal_places' => 3, 'description' => 'Yarn, fabric roll weight measurement', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'DZN', 'name' => 'Dozens', 'symbol' => 'dz', 'decimal_places' => 1, 'description' => '12 pieces bundle packing', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'ROL', 'name' => 'Rolls', 'symbol' => 'roll', 'decimal_places' => 0, 'description' => 'Fabric rolls / elastic / tape rolls', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'BOX', 'name' => 'Boxes / Cartons', 'symbol' => 'box', 'decimal_places' => 0, 'description' => 'Export corrugated master boxes', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'YRD', 'name' => 'Yards', 'symbol' => 'yd', 'decimal_places' => 2, 'description' => 'Imported fabric length measurement', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'CONE', 'name' => 'Thread Cones', 'symbol' => 'cone', 'decimal_places' => 0, 'description' => 'Sewing & embroidery thread spools', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'GRS', 'name' => 'Gross', 'symbol' => 'grs', 'decimal_places' => 0, 'description' => '144 units (buttons, rivets, eyelets)', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'SET', 'name' => 'Sets', 'symbol' => 'set', 'decimal_places' => 0, 'description' => 'Matched top + bottom 2pc/3pc sets', 'status' => 'Active', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
