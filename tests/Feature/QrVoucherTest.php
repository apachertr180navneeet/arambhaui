<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\QrVoucher;
use Tests\TestCase;

class QrVoucherTest extends TestCase
{
    protected function getAuthenticatedUser(): User
    {
        return User::where('email', 'admin@garmenterp.com')->first()
            ?? User::factory()->create(['role' => 'admin', 'status' => 'active']);
    }

    /**
     * Test admin can create a new QR voucher.
     */
    public function test_admin_can_generate_qr_voucher(): void
    {
        $user = $this->getAuthenticatedUser();
        $code = 'TEST-SAVE-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $response = $this->actingAs($user)->postJson('/qr/generator', [
            'voucher_code' => $code,
            'customer_name' => 'Rahul Verma',
            'customer_phone' => '+91 98201 12345',
            'discount_type' => 'Flat',
            'discount_amount' => 500,
            'max_discount_cap' => 5000,
            'min_order_value' => 1000,
            'valid_until' => now()->addDays(30)->toDateString()
        ]);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'voucher' => [
                         'voucher_code' => $code,
                         'discount_amount' => 500,
                         'status' => 'Active',
                         'is_redeemed' => false
                     ]
                 ]);

        $this->assertDatabaseHas('qr_vouchers', [
            'voucher_code' => $code,
            'status' => 'Active',
            'is_redeemed' => 0
        ]);

        QrVoucher::where('voucher_code', $code)->delete();
    }

    /**
     * Test customer can validate active voucher.
     */
    public function test_can_validate_active_voucher(): void
    {
        $user = $this->getAuthenticatedUser();
        $code = 'VAL-TEST-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $voucher = QrVoucher::create([
            'voucher_code' => $code,
            'customer_name' => 'Sunil Gupta',
            'discount_type' => 'Percentage',
            'discount_percent' => 15,
            'max_discount_cap' => 2000,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(15)->toDateString(),
            'status' => 'Active',
            'is_redeemed' => false
        ]);

        $response = $this->actingAs($user)->postJson('/qr/validate', [
            'voucher_code' => $code
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'discount_percent' => 15
                 ]);

        $voucher->delete();
    }

    /**
     * Test single-use redemption links phone and permanently expires voucher.
     */
    public function test_redeem_voucher_expires_upon_single_use(): void
    {
        $user = $this->getAuthenticatedUser();
        $code = 'SINGLE-USE-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $voucher = QrVoucher::create([
            'voucher_code' => $code,
            'customer_name' => 'Amit Shah',
            'discount_type' => 'Flat',
            'discount_amount' => 500,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(10)->toDateString(),
            'status' => 'Active',
            'is_redeemed' => false
        ]);

        // 1. First redemption attempt -> SUCCESS
        $response = $this->actingAs($user)->postJson('/qr/redeem', [
            'voucher_code' => $code,
            'customer_phone' => '+91 98765 43210',
            'order_bill' => 3000
        ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'discount_value' => 500,
                     'final_payable' => 2500
                 ]);

        // Verify in Database
        $voucher->refresh();
        $this->assertTrue((bool)$voucher->is_redeemed);
        $this->assertEquals('Redeemed', $voucher->status);
        $this->assertEquals('+91 98765 43210', $voucher->customer_phone);
        $this->assertNotNull($voucher->redeemed_at);

        // 2. Second redemption attempt on same single-use voucher -> BLOCKED & REJECTED
        $secondResponse = $this->actingAs($user)->postJson('/qr/redeem', [
            'voucher_code' => $code,
            'customer_phone' => '+91 98765 43210',
            'order_bill' => 3000
        ]);

        $secondResponse->assertStatus(422)
                       ->assertJson([
                           'success' => false,
                           'already_redeemed' => true
                       ]);

        $voucher->delete();
    }

    /**
     * Test admin can expire and reactivate vouchers.
     */
    public function test_admin_can_expire_and_reactivate_voucher(): void
    {
        $user = $this->getAuthenticatedUser();
        $code = 'MANUAL-EXP-' . strtoupper(substr(md5(uniqid()), 0, 6));

        $voucher = QrVoucher::create([
            'voucher_code' => $code,
            'valid_from' => now()->toDateString(),
            'valid_until' => now()->addDays(5)->toDateString(),
            'status' => 'Active',
            'is_redeemed' => false
        ]);

        // Expire
        $this->actingAs($user)->postJson("/qr/expire/{$voucher->id}")
             ->assertStatus(200);

        $voucher->refresh();
        $this->assertEquals('Expired', $voucher->status);

        // Reactivate
        $this->actingAs($user)->postJson("/qr/reactivate/{$voucher->id}")
             ->assertStatus(200);

        $voucher->refresh();
        $this->assertEquals('Active', $voucher->status);
        $this->assertFalse((bool)$voucher->is_redeemed);

        $voucher->delete();
    }
}
