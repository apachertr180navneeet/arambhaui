<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Item;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class PurchaseRateAndItemMasterTest extends TestCase
{
    use DatabaseTransactions;

    protected $user;
    protected $vendor;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::first();
        if (!$this->user) {
            $this->user = User::create([
                'name' => 'Admin Test',
                'email' => 'admin_test@fashionworks.com',
                'password' => bcrypt('admin123'),
                'role' => 'Administrator',
                'status' => 'Active'
            ]);
        }
        $this->actingAs($this->user);

        $this->vendor = Vendor::first();
        if (!$this->vendor) {
            $this->vendor = Vendor::create([
                'name' => 'Textile Mill Supplier',
                'contact_person' => 'Rajesh Kumar',
                'mobile' => '9876543210',
                'city' => 'Surat',
                'status' => 'Active'
            ]);
        }
    }

    /**
     * Scenario 1: Create an item without a Standard Unit Cost.
     */
    public function test_can_create_item_without_standard_unit_cost()
    {
        $payload = [
            'name' => 'Modal Satin 80 GSM',
            'unit' => 'Meters',
            'hsn_code' => '5407',
            'category' => 'Fabric',
            'status' => 'Active'
        ];

        $response = $this->postJson(route('masters.items.store'), $payload);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $item = Item::where('name', 'Modal Satin 80 GSM')->first();
        $this->assertNotNull($item);
        $this->assertEquals(0.00, (float)$item->unit_cost);
        $this->assertEquals('Meters', $item->unit);
        $this->assertEquals('5407', $item->hsn_code);
    }

    /**
     * Scenarios 2, 3, 4, 5:
     * - Create purchase entry with rate of ₹120.
     * - Create another purchase entry for the same item with rate of ₹135.
     * - Verify both purchase bills retain their respective rates.
     * - Verify quantity, line amount, GST, and invoice totals.
     */
    public function test_purchase_entries_use_current_rate_and_retain_independent_rates()
    {
        // 1. Create item without unit_cost
        $item = Item::create([
            'code' => 'FAB-COTTON-001',
            'name' => 'Pure Cambric Cotton 60s',
            'unit' => 'Meters',
            'category' => 'Fabric',
            'status' => 'Active',
            'unit_cost' => 0.00,
            'current_stock' => 0.00,
            'min_stock' => 0.00
        ]);

        // 2. Purchase Bill 1: 100 meters @ ₹120/meter, 5% GST
        $payload1 = [
            'vendor_name' => $this->vendor->name,
            'vendor_id' => $this->vendor->id,
            'challan_no' => 'CH-TEST-120',
            'po_date' => '2026-10-01',
            'items' => [
                [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'item_code' => $item->code,
                    'unit' => 'Meters',
                    'rate' => 120.00,
                    'tax_percent' => 5.0,
                    'ordered_qty' => 100.0,
                    'thans' => [50.0, 50.0]
                ]
            ]
        ];

        $res1 = $this->postJson(route('purchase.orders.store'), $payload1);
        $res1->assertStatus(200);
        $res1->assertJson(['success' => true]);

        $po1 = PurchaseOrder::where('challan_no', 'CH-TEST-120')->first();
        $this->assertNotNull($po1);
        $lineItem1 = $po1->items()->first();

        // Line 1 validations: 100m * ₹120 = ₹12,000 subtotal, 5% GST = ₹600, total = ₹12,600
        $this->assertEquals(120.00, (float)$lineItem1->rate);
        $this->assertEquals(100.00, (float)$lineItem1->ordered_qty);
        $this->assertEquals(12600.00, (float)$lineItem1->total_amount);
        $this->assertEquals(12000.00, (float)$po1->subtotal);
        $this->assertEquals(600.00, (float)$po1->tax_total);
        $this->assertEquals(12600.00, (float)$po1->grand_total);

        // 3. Purchase Bill 2: 200 meters @ ₹135/meter, 5% GST
        $payload2 = [
            'vendor_name' => $this->vendor->name,
            'vendor_id' => $this->vendor->id,
            'challan_no' => 'CH-TEST-135',
            'po_date' => '2026-10-10',
            'items' => [
                [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'item_code' => $item->code,
                    'unit' => 'Meters',
                    'rate' => 135.00,
                    'tax_percent' => 5.0,
                    'ordered_qty' => 200.0,
                    'thans' => [100.0, 100.0]
                ]
            ]
        ];

        $res2 = $this->postJson(route('purchase.orders.store'), $payload2);
        $res2->assertStatus(200);
        $res2->assertJson(['success' => true]);

        $po2 = PurchaseOrder::where('challan_no', 'CH-TEST-135')->first();
        $this->assertNotNull($po2);
        $lineItem2 = $po2->items()->first();

        // Line 2 validations: 200m * ₹135 = ₹27,000 subtotal, 5% GST = ₹1,350, total = ₹28,350
        $this->assertEquals(135.00, (float)$lineItem2->rate);
        $this->assertEquals(200.00, (float)$lineItem2->ordered_qty);
        $this->assertEquals(28350.00, (float)$lineItem2->total_amount);
        $this->assertEquals(27000.00, (float)$po2->subtotal);
        $this->assertEquals(1350.00, (float)$po2->tax_total);
        $this->assertEquals(28350.00, (float)$po2->grand_total);

        // 4. Verify historical bill 1 retained its original rate ₹120 and amounts
        $lineItem1->refresh();
        $po1->refresh();
        $this->assertEquals(120.00, (float)$lineItem1->rate);
        $this->assertEquals(12600.00, (float)$po1->grand_total);

        // 5. Verify historical bill 2 retained its original rate ₹135 and amounts
        $lineItem2->refresh();
        $po2->refresh();
        $this->assertEquals(135.00, (float)$lineItem2->rate);
        $this->assertEquals(28350.00, (float)$po2->grand_total);
    }

    /**
     * Scenario 6: Edit a purchase entry and confirm its original rate is displayed correctly.
     */
    public function test_edit_purchase_entry_displays_original_rate()
    {
        $item = Item::create([
            'code' => 'FAB-RAYON-777',
            'name' => 'Rayon 14kg Twill',
            'unit' => 'Meters',
            'category' => 'Fabric',
            'status' => 'Active',
            'unit_cost' => 0.00
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PE-TEST-777',
            'challan_no' => 'CH-ORIGINAL-RATE',
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'po_date' => '2026-10-05',
            'subtotal' => 12000.00,
            'tax_total' => 600.00,
            'grand_total' => 12600.00,
            'status' => 'Approved',
            'payment_status' => 'Pending'
        ]);

        PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_code' => $item->code,
            'ordered_qty' => 100.0,
            'unit' => 'Meters',
            'rate' => 120.00,
            'tax_percent' => 5.0,
            'tax_amount' => 600.00,
            'total_amount' => 12600.00
        ]);

        $response = $this->get(route('purchase.orders.edit', $po->id));
        $response->assertStatus(200);
        // Ensure the original rate ₹120 is present in the rendered edit screen
        $response->assertSee('120');
        $response->assertSee('Rayon 14kg Twill');
    }

    /**
     * Scenario 7: Confirm Item Master changes do not alter historical purchase records.
     */
    public function test_item_master_changes_do_not_alter_historical_purchase_records()
    {
        $item = Item::create([
            'code' => 'FAB-CHANDERI-101',
            'name' => 'Chanderi Silk Cotton',
            'unit' => 'Meters',
            'category' => 'Fabric',
            'status' => 'Active',
            'unit_cost' => 0.00
        ]);

        $po = PurchaseOrder::create([
            'po_number' => 'PE-TEST-888',
            'challan_no' => 'CH-HIST-CHECK',
            'vendor_id' => $this->vendor->id,
            'vendor_name' => $this->vendor->name,
            'po_date' => '2026-09-15',
            'subtotal' => 24000.00,
            'tax_total' => 1200.00,
            'grand_total' => 25200.00,
            'status' => 'Approved'
        ]);

        $poItem = PurchaseOrderItem::create([
            'purchase_order_id' => $po->id,
            'item_id' => $item->id,
            'item_name' => $item->name,
            'item_code' => $item->code,
            'ordered_qty' => 200.0,
            'unit' => 'Meters',
            'rate' => 120.00,
            'tax_percent' => 5.0,
            'tax_amount' => 1200.00,
            'total_amount' => 25200.00
        ]);

        // Now update Item Master name, unit, etc.
        $this->putJson(route('masters.items.update', $item->id), [
            'name' => 'Chanderi Silk Cotton Superfine',
            'unit' => 'Meters',
            'hsn_code' => '5007'
        ]);

        // Historical purchase record must remain untouched
        $poItem->refresh();
        $this->assertEquals(120.00, (float)$poItem->rate);
        $this->assertEquals(25200.00, (float)$poItem->total_amount);

        $po->refresh();
        $this->assertEquals(25200.00, (float)$po->grand_total);
    }
}
