<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function jobWorker()
    {
        return $this->belongsTo(JobWorker::class);
    }

    public function items()
    {
        return $this->hasMany(JobAssignmentItem::class);
    }

    public function inwards()
    {
        return $this->hasMany(JobInward::class);
    }

    public function getThanListAttribute(): array
    {
        if (!empty($this->than_details)) {
            $decoded = is_string($this->than_details) ? json_decode($this->than_details, true) : $this->than_details;
            if (is_array($decoded)) return $decoded;
        }
        $all = [];
        if ($this->relationLoaded('items') || $this->items()->exists()) {
            foreach ($this->items as $itm) {
                $itemThans = $itm->than_list;
                if (!empty($itemThans)) {
                    $all = array_merge($all, $itemThans);
                }
            }
        }
        return $all;
    }

    public function getTotalThansCountAttribute(): int
    {
        if (isset($this->attributes['total_thans']) && (int)$this->attributes['total_thans'] > 0) {
            return (int)$this->attributes['total_thans'];
        }
        return count($this->than_list);
    }
}
