<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DispatchItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function dispatchChallan()
    {
        return $this->belongsTo(DispatchChallan::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function getThansListAttribute(): array
    {
        if (!empty($this->thans_data)) {
            $decoded = is_string($this->thans_data) ? json_decode($this->thans_data, true) : $this->thans_data;
            if (is_array($decoded)) return $decoded;
        }
        return [];
    }
}
