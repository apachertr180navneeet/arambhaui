<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobWorker extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function assignments()
    {
        return $this->hasMany(JobAssignment::class);
    }
}
