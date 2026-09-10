<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;

class BladeViewsTest extends TestCase
{
    public function test_all_blade_routes_render_cleanly()
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

        $routes = [
            '/dashboard',
            '/masters/customers',
            '/masters/vendors',
            '/masters/jobworkers',
            '/masters/items',
            '/masters/units',
            '/purchase/orders',
            '/purchase/orders/create',
            '/jobwork/assign',
            '/jobwork/inward-report',
            '/qr/generator',
            '/qr/scanner',
            '/qr/history',
            '/dispatch/ready',
            '/dispatch/dispatch',
            '/accounts/customer-accounts',
            '/accounts/customer-outstanding',
            '/accounts/vendor-outstanding',
            '/accounts/jobworker-outstanding',
            '/reports/ledger',
            '/reports/stock',
            '/reports/lot-purchase',
            '/reports/lot-sales',
            '/admin/users',
            '/admin/roles',
            '/admin/activity',
            '/admin/settings'
        ];

        foreach ($routes as $uri) {
            $response = $this->actingAs($user)->get($uri);
            $response->assertStatus(200);
        }
    }
}
