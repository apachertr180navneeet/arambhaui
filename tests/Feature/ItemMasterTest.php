<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ItemMasterTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        // If auth is required, authenticate as first user or admin
        $user = User::first();
        if ($user) {
            $this->actingAs($user);
        }
    }

    /**
     * Test Item Master page renders without errors.
     */
    public function test_item_master_page_renders_properly()
    {
        $response = $this->get(route('masters.items.index'));
        $response->assertStatus(200);
        $response->assertSee('Item Description / Quality');
        $response->assertSee('Unit of Measurement');
        $response->assertSee('HSN Code');
        // Ensure removed fields are not present in the creation/edit form
        $response->assertDontSee('id="itm_unit_cost"', false);
        $response->assertDontSee('id="itm_opening_stock"', false);
        $response->assertDontSee('id="itm_min_stock"', false);
        $response->assertDontSee('id="itm_location"', false);
    }

    /**
     * Test creating an item with only the essential fields (name, unit, hsn_code).
     */
    public function test_can_create_item_with_only_essential_fields()
    {
        $payload = [
            'name' => 'Premium Rayon 14kg Liva',
            'unit' => 'Meters',
            'hsn_code' => '5407',
        ];

        $response = $this->postJson(route('masters.items.store'), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $this->assertDatabaseHas('items', [
            'name' => 'Premium Rayon 14kg Liva',
            'unit' => 'Meters',
            'hsn_code' => '5407',
            'unit_cost' => 0.00,
            'current_stock' => 0.00,
        ]);
    }

    /**
     * Test updating an item with essential fields does not wipe existing stock/cost.
     */
    public function test_can_update_item_without_modifying_stock_or_cost()
    {
        $item = Item::create([
            'code' => 'TEST-ITM-999',
            'name' => 'Original Cotton Grey',
            'category' => 'Fabric',
            'unit' => 'Meters',
            'unit_cost' => 150.50,
            'current_stock' => 500.00,
            'min_stock' => 50.00,
            'location' => 'Rack A-12',
            'status' => 'Active',
        ]);

        $payload = [
            'name' => 'Updated Cotton Grey 60s',
            'unit' => 'Meters',
            'hsn_code' => '5208',
        ];

        $response = $this->putJson(route('masters.items.update', $item->id), $payload);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
        ]);

        $item->refresh();
        $this->assertEquals('Updated Cotton Grey 60s', $item->name);
        $this->assertEquals('5208', $item->hsn_code);
        // Ensure historical stock and cost are preserved
        $this->assertEquals(150.50, (float)$item->unit_cost);
        $this->assertEquals(500.00, (float)$item->current_stock);
        $this->assertEquals(50.00, (float)$item->min_stock);
        $this->assertEquals('Rack A-12', $item->location);
    }

    /**
     * Test validation requires name.
     */
    public function test_validation_fails_when_name_missing()
    {
        $payload = [
            'unit' => 'Meters',
        ];

        $response = $this->postJson(route('masters.items.store'), $payload);
        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['name']);
    }
}
