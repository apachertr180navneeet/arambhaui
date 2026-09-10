<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;

class CustomerSoftDeleteTest extends TestCase
{
    public function test_customer_is_soft_deleted()
    {
        $code = 'CUST-TEST-' . uniqid();
        Customer::withTrashed()->where('code', $code)->forceDelete();

        $admin = User::first() ?? User::create([
            'name' => 'Admin User',
            'email' => 'admin_test_' . uniqid() . '@garmenterp.com',
            'password' => bcrypt('admin123'),
            'role' => 'Administrator',
            'status' => 'Active'
        ]);

        $customer = Customer::create([
            'code' => $code,
            'name' => 'Soft Delete Test Customer',
            'phone' => '9999999999',
            'status' => 'Active'
        ]);

        $response = $this->actingAs($admin)
            ->deleteJson(route('masters.customers.destroy', $customer));

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        // Verify record is soft deleted (deleted_at is populated, record retained in DB)
        $this->assertSoftDeleted('customers', [
            'id' => $customer->id,
            'name' => 'Soft Delete Test Customer'
        ]);

        // Standard query should not return this soft-deleted customer
        $this->assertNull(Customer::find($customer->id));

        // withTrashed query should still return this customer
        $this->assertNotNull(Customer::withTrashed()->find($customer->id));

        // Clean up
        $customer->forceDelete();
    }
}
