<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class VendorPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'float'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function allocations()
    {
        return $this->hasMany(PurchasePaymentAllocation::class, 'vendor_payment_id');
    }

    public function getAllocatedAmountAttribute(): float
    {
        if ($this->relationLoaded('allocations')) {
            return (float) $this->allocations->sum('allocated_amount');
        }
        return (float) $this->allocations()->sum('allocated_amount');
    }

    public function getUnallocatedAmountAttribute(): float
    {
        return (float) max(0, round($this->amount - $this->allocated_amount, 2));
    }
}
