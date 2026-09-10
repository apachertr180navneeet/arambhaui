<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class QrVoucher extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'is_redeemed' => 'boolean',
        'redeemed_at' => 'datetime',
        'valid_from' => 'date',
        'valid_until' => 'date',
        'discount_percent' => 'float',
        'discount_amount' => 'float',
        'max_discount_cap' => 'float',
        'min_order_value' => 'float',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
