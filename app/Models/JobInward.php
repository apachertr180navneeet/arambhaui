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
}
