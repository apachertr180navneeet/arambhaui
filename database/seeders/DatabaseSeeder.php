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
        User::updateOrCreate(
            ['email' => 'admin@garmenterp.com'],
            [
                'name' => 'System Administrator',
                'role' => 'admin',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'supervisor@garmenterp.com'],
            [
                'name' => 'Production Supervisor',
                'role' => 'supervisor',
                'status' => 'active',
                'password' => Hash::make('admin123'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Customers
        $c1 = Customer::updateOrCreate(
            ['code' => 'CUST-001'],
            [
                'name' => 'Vogue Fashions Mumbai',
                'company_name' => 'Vogue Retail Ltd.',
                'contact_person' => 'Rajesh Sharma',
                'phone' => '+91 98201 54321',
                'email' => 'rajesh@voguefashions.in',
                'gstin' => '27AABCV1234F1Z8',
                'address' => 'Plot 45, MIDC Andheri East, Mumbai, Maharashtra 400093',
                'city' => 'Mumbai',
                'credit_limit' => 1500000.00,
                'outstanding' => 245000.00,
                'payment_terms' => 'Net 30 Days',
                'status' => 'Active'
            ]
        );

        $c2 = Customer::updateOrCreate(
            ['code' => 'CUST-002'],
            [
                'name' => 'Urban Trends Pune',
                'company_name' => 'Urban Trends Apparel',
                'contact_person' => 'Neha Joshi',
                'phone' => '+91 98450 67890',
                'email' => 'neha@urbantrends.co.in',
                'gstin' => '27AAACU9876E1Z4',
                'address' => 'FC Road, Deccan Gymkhana, Pune 411004',
                'city' => 'Pune',
                'credit_limit' => 800000.00,
                'outstanding' => 120000.00,
                'payment_terms' => 'Net 15 Days',
                'status' => 'Active'
            ]
        );

        // 3. Vendors
        $v1 = Vendor::updateOrCreate(
            ['code' => 'VEND-001'],
            [
                'name' => 'Surat Textiles Mills',
                'company_name' => 'Surat Weaving Mills Ltd.',
                'contact_person' => 'Ketan Patel',
                'phone' => '+91 98795 11223',
                'email' => 'orders@surattextiles.com',
                'gstin' => '24AABCS4455P1ZX',
                'category' => 'Fabric Supplier',
                'credit_days' => 45,
                'outstanding' => 380000.00,
                'status' => 'Active'
            ]
        );

        // 4. Job Workers
        JobWorker::updateOrCreate(
            ['code' => 'JW-001'],
            [
                'name' => 'Anand Stitching Hub',
                'phone' => '+91 98220 99887',
                'skill_type' => 'Stitching',
                'rate_per_piece' => 18.50,
                'daily_capacity' => 400,
                'address' => 'Gala 12, Industrial Estate, Bhiwandi',
                'outstanding' => 45000.00,
                'status' => 'Active'
            ]
        );

        JobWorker::updateOrCreate(
            ['code' => 'JW-002'],
            [
                'name' => 'Master Cutters Unit',
                'phone' => '+91 98221 44332',
                'skill_type' => 'Cutting',
                'rate_per_piece' => 8.00,
                'daily_capacity' => 800,
                'address' => 'Unit 3, MIDC Boisar',
                'outstanding' => 22000.00,
                'status' => 'Active'
            ]
        );

        // 5. Items
        Item::updateOrCreate(
            ['code' => 'FAB-001'],
            [
                'name' => '100% Cotton Poplin 60s (Navy Blue)',
                'category' => 'Fabric',
                'unit' => 'Meters',
                'hsn_code' => '5208',
                'unit_cost' => 145.00,
                'current_stock' => 3450.00,
                'min_stock' => 500.00,
                'location' => 'Zone A - Rack 3',
                'status' => 'Active'
            ]
        );

        Item::updateOrCreate(
            ['code' => 'TRM-001'],
            [
                'name' => 'Polyester 4-Hole Buttons (14L)',
                'category' => 'Trims',
                'unit' => 'Gross',
                'hsn_code' => '9606',
                'unit_cost' => 85.00,
                'current_stock' => 120.00,
                'min_stock' => 20.00,
                'location' => 'Zone C - Bin 14',
                'status' => 'Active'
            ]
        );

        // 6. Sizes & Colors
        $sizes = ['XS', 'S', 'M', 'L', 'XL', '2XL', '3XL'];
        foreach ($sizes as $idx => $s) {
            Size::updateOrCreate(['name' => $s], ['sort_order' => $idx + 1]);
        }

        $colors = [
            ['name' => 'Navy Blue', 'hex_code' => '#1e3a8a'],
            ['name' => 'Crimson Red', 'hex_code' => '#dc2626'],
            ['name' => 'Olive Green', 'hex_code' => '#65a30d'],
            ['name' => 'Charcoal Grey', 'hex_code' => '#374151'],
            ['name' => 'Pure White', 'hex_code' => '#ffffff'],
        ];
        foreach ($colors as $c) {
            Color::updateOrCreate(['name' => $c['name']], ['hex_code' => $c['hex_code']]);
        }

        // 7. QR Vouchers
        QrVoucher::updateOrCreate(
            ['voucher_code' => 'FASHION-15-FEST26'],
            [
                'customer_name' => 'Vogue Fashions Mumbai',
                'title' => 'Seasonal Festive Discount',
                'discount_type' => 'Percentage',
                'discount_percent' => 15.00,
                'max_discount_cap' => 15000.00,
                'min_order_value' => 50000.00,
                'valid_from' => '2026-01-01',
                'valid_until' => '2026-12-31',
                'status' => 'Active'
            ]
        );

        // 8. Company Settings
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

        // 9. Initial Activity Log
        ActivityLog::create([
            'user_name' => 'System Administrator',
            'action' => 'Initialized',
            'module' => 'System',
            'description' => 'GarmentERP database schema and starter data initialized.'
        ]);
    }
}
