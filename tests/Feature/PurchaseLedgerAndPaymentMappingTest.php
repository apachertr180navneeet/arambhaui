<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Vendor;
use App\Models\PurchaseOrder;
use App\Models\VendorPayment;
use App\Models\PurchasePaymentAllocation;

class PurchaseLedgerAndPaymentMappingTest extends TestCase
{
    protected function getAuthUser(): User
    {
        $user = User::first();
        if (!$user) {
            $user = User::create([
                'name' => 'Admin Test',
                'email' => 'admin_test@fashionworks.com',
                'password' => 'admin123',
                'role' => 'Administrator',
                'status' => 'Active'
            ]);
        }
        return $user;
    }

    public function test_purchase_ledger_renders_tally_layout()
    {
        $user = $this->getAuthUser();
        $response = $this->actingAs($user)->get(route('purchase.ledger'));
        
        $response->assertStatus(200);
        $response->assertSee('Purchase Ledger');
        $response->assertSee('Accounting Statement');
        $response->assertSee('Total Purchase Amount');
    }

    public function test_purchase_ledger_csv_export()
    {
        $user = $this->getAuthUser();
        $response = $this->actingAs($user)->get(route('purchase.ledger', ['export' => 'csv']));
        
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_payment_mapping_screen_renders()
    {
        $user = $this->getAuthUser();
        $response = $this->actingAs($user)->get(route('purchase.payment-mapping'));
        
        $response->assertStatus(200);
        $response->assertSee('Payment Mapping');
        $response->assertSee('Purchase Bills');
        $response->assertSee('Payment Records');
    }
}
