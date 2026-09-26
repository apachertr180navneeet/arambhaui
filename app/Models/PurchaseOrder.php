<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'subtotal' => 'float',
        'tax_total' => 'float',
        'grand_total' => 'float'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function getThanListAttribute()
    {
        if (!empty($this->than_details)) {
            $val = is_array($this->than_details) ? $this->than_details : json_decode($this->than_details, true);
            if (is_array($val)) return $val;
        }
        if (!empty($this->notes) && str_starts_with(trim($this->notes), '{')) {
            $data = json_decode($this->notes, true);
            if (isset($data['thans']) && is_array($data['thans'])) return $data['thans'];
        }
        // If items are loaded, collect all thans from items
        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            $allThans = [];
            foreach ($this->items as $item) {
                $thans = $item->than_list;
                if (!empty($thans)) {
                    $allThans = array_merge($allThans, $thans);
                }
            }
            if (!empty($allThans)) return $allThans;
        }
        return [];
    }

    public function getChallanNumberAttribute()
    {
        if (!empty($this->challan_no)) return $this->challan_no;
        if (!empty($this->notes) && str_starts_with(trim($this->notes), '{')) {
            $data = json_decode($this->notes, true);
            if (!empty($data['challan_no'])) return $data['challan_no'];
        }
        return '';
    }

    public function getTotalThansCountAttribute()
    {
        if (!empty($this->total_thans)) return (int)$this->total_thans;
        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            $sum = $this->items->sum('than_count');
            if ($sum > 0) return (int)$sum;
        }
        $list = $this->than_list;
        if (!empty($list) && is_array($list)) return count($list);
        return 0;
    }

    public function getTotalMetersSumAttribute()
    {
        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            return (float)$this->items->sum('ordered_qty');
        }
        $firstItem = $this->items()->first();
        if ($firstItem) {
            return (float)$firstItem->ordered_qty;
        }
        return 0.0;
    }

    public function getItemsSummaryAttribute()
    {
        if ($this->relationLoaded('items') && $this->items->isNotEmpty()) {
            $names = $this->items->pluck('item_name')->filter()->unique()->values();
            if ($names->count() === 1) {
                return $names->first();
            } elseif ($names->count() > 1) {
                return $names->first() . ' (+' . ($names->count() - 1) . ' more)';
            }
        }
        return 'Fabric Quality';
    }
}
