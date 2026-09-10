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
}
