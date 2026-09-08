<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Unit;
use Illuminate\Support\Facades\Schema;

class UnitSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();
        Unit::truncate();
        Schema::enableForeignKeyConstraints();

        $pair = Unit::create([
            'code' => 'PAIR',
            'name' => 'PAIR',
            'symbol' => 'pr',
            'decimal_places' => 0,
            'status' => 'Active'
        ]);

        $pcs = Unit::create([
            'code' => 'PCS',
            'name' => 'PCS',
            'symbol' => 'pcs',
            'decimal_places' => 0,
            'status' => 'Active'
        ]);

        $kg = Unit::create([
            'code' => 'KG',
            'name' => 'KG',
            'symbol' => 'kg',
            'decimal_places' => 3,
            'status' => 'Active'
        ]);

        $gm = Unit::create([
            'code' => 'gm',
            'name' => 'gm',
            'parent_id' => $kg->id,
            'conversion_factor' => 1000.00,
            'symbol' => 'gm',
            'decimal_places' => 2,
            'status' => 'Active'
        ]);

        $mtr = Unit::create([
            'code' => 'MTR',
            'name' => 'MTR',
            'symbol' => 'mtr',
            'decimal_places' => 2,
            'status' => 'Active'
        ]);

        $dzn = Unit::create([
            'code' => 'DZN',
            'name' => 'DZN',
            'symbol' => 'dz',
            'decimal_places' => 1,
            'status' => 'Active'
        ]);
    }
}
