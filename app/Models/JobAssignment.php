<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function jobWorker()
    {
        return $this->belongsTo(JobWorker::class);
    }

    public function items()
    {
        return $this->hasMany(JobAssignmentItem::class);
    }
}
