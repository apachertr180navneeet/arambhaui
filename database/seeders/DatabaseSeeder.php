<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Customer;
use App\Models\Vendor;
use App\Models\JobWorker;
use App\Models\Item;
use App\Models\Size;
use App\Models\Color;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseInward;
use App\Models\JobAssignment;
use App\Models\JobAssignmentItem;
use App\Models\ProductionOrder;
use App\Models\QualityCheck;
use App\Models\LotTracking;
use App\Models\QrVoucher;
use App\Models\DispatchChallan;
use App\Models\DispatchItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\CustomerPayment;
use App\Models\CompanySetting;
use App\Models\ActivityLog;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with enterprise starter data.
     */
    public function run(): void
    {
        // 1. Users
        $this->call(UserSeeder::class);

        // 2. Company Settings
        $settings = [
            'company_name' => 'FashionWorks Garments Pvt. Ltd.',
            'gstin' => '27AABCF9876K1Z2',
            'phone' => '+91 22 6789 0000',
            'email' => 'erp@fashionworks.co.in',
            'address' => 'Plot 101, Textile Technology Park, Bhiwandi, Thane 421302',
            'currency_symbol' => '₹',
            'financial_year' => '2026-2027'
        ];
        foreach ($settings as $k => $v) {
            CompanySetting::updateOrCreate(['key' => $k], ['value' => $v]);
        }

        // 3. Initial Activity Log
        ActivityLog::create([
            'user_name' => 'System Administrator',
            'action' => 'Initialized',
            'module' => 'System',
            'description' => 'GarmentERP database schema initialized.'
        ]);
    }
}
