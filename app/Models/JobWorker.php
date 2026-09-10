<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWorker extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function assignments()
    {
        return $this->hasMany(JobAssignment::class);
    }
}
