<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAssignmentItem extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'than_meters' => 'float',
        'production_pcs' => 'integer',
        'wastage_meters' => 'float',
        'avg_consumption' => 'float',
        'rate_per_piece' => 'float',
        'total_amount' => 'float',
        'than_count' => 'integer',
        'qty' => 'integer'
    ];

    public function jobAssignment()
    {
        return $this->belongsTo(JobAssignment::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function rawItem()
    {
        return $this->belongsTo(Item::class, 'raw_item_id');
    }

    public function finishedItem()
    {
        return $this->belongsTo(Item::class, 'finished_item_id');
    }

    public function getThanListAttribute(): array
    {
        if (!empty($this->than_details)) {
            $decoded = is_string($this->than_details) ? json_decode($this->than_details, true) : $this->than_details;
            if (is_array($decoded)) return $decoded;
        }
        if ($this->than_meters > 0) {
            return [(float)$this->than_meters];
        }
        return [];
    }
}
