<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobInward extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function jobAssignment()
    {
        return $this->belongsTo(JobAssignment::class, 'job_assignment_id');
    }

    public function jobWorker()
    {
        return $this->belongsTo(JobWorker::class, 'job_worker_id');
    }

    public function getThanListAttribute(): array
    {
        if (!empty($this->than_details)) {
            $decoded = is_string($this->than_details) ? json_decode($this->than_details, true) : $this->than_details;
            if (is_array($decoded)) return $decoded;
        }
        if (!empty($this->remarks) && is_string($this->remarks)) {
            $data = json_decode($this->remarks, true);
            if (is_array($data) && !empty($data['thans'])) return $data['thans'];
        }
        return [];
    }

    public function getItemsListAttribute(): array
    {
        if (!empty($this->items_data)) {
            $decoded = is_string($this->items_data) ? json_decode($this->items_data, true) : $this->items_data;
            if (is_array($decoded)) return $decoded;
        }
        if (!empty($this->remarks) && is_string($this->remarks)) {
            $data = json_decode($this->remarks, true);
            if (is_array($data) && !empty($data['items'])) return $data['items'];
        }
        return [];
    }

    public function getTotalThansCountAttribute(): int
    {
        if (isset($this->attributes['total_thans']) && (int)$this->attributes['total_thans'] > 0) {
            return (int)$this->attributes['total_thans'];
        }
        return count($this->than_list);
    }

    public function getTotalMetersCountAttribute(): float
    {
        if (isset($this->attributes['total_meters']) && (float)$this->attributes['total_meters'] > 0) {
            return (float)$this->attributes['total_meters'];
        }
        $thans = $this->than_list;
        if (!empty($thans)) {
            return (float)array_sum($thans);
        }
        return (float)($this->wastage_returned_meters ?? 0);
    }

    public function getUserRemarksAttribute(): string
    {
        if (!empty($this->remarks) && is_string($this->remarks)) {
            $data = json_decode($this->remarks, true);
            if (is_array($data)) {
                $userRemarks = $data['user_remarks'] ?? '';
                if (is_string($userRemarks) && !empty($userRemarks)) {
                    $nested = json_decode($userRemarks, true);
                    if (is_array($nested) && array_key_exists('user_remarks', $nested)) {
                        return (string)($nested['user_remarks'] ?? '');
                    }
                    return $userRemarks;
                }
                return '';
            }
            return (string)$this->remarks;
        }
        return '';
    }
}
