<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrderItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'ordered_qty' => 'float',
        'received_qty' => 'float',
        'rate' => 'float',
        'tax_percent' => 'float',
        'tax_amount' => 'float',
        'total_amount' => 'float',
        'than_count' => 'integer'
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getThanListAttribute()
    {
        if (!empty($this->than_details)) {
            $val = is_array($this->than_details) ? $this->than_details : json_decode($this->than_details, true);
            if (is_array($val)) return $val;
        }
        if ($this->ordered_qty > 0 && ($this->than_count == 1 || empty($this->than_count))) {
            return [(float)$this->ordered_qty];
        }
        return [];
    }
}
