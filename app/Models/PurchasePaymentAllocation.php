<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchasePaymentAllocation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'allocated_amount' => 'float'
    ];

    public function payment()
    {
        return $this->belongsTo(VendorPayment::class, 'vendor_payment_id');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class, 'purchase_order_id');
    }
}
