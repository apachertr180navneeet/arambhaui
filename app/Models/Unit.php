<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'name',
        'parent_id',
        'conversion_factor',
        'symbol',
        'decimal_places',
        'description',
        'status'
    ];

    protected $casts = [
        'decimal_places' => 'integer',
        'conversion_factor' => 'float',
        'parent_id' => 'integer'
    ];

    public function parent()
    {
        return $this->belongsTo(Unit::class, 'parent_id');
    }

    public function subUnits()
    {
        return $this->hasMany(Unit::class, 'parent_id');
    }

    public function getDecimalPlacesAttribute($value)
    {
        if ($value === null || $value === '') {
            return 0;
        }
        $code = strtoupper((string)($this->attributes['code'] ?? ''));
        $name = strtoupper((string)($this->attributes['name'] ?? ''));
        if ((int)$value === 2 && (in_array($code, ['PCS', 'PC']) || in_array($name, ['PCS', 'PIECES']))) {
            return 0;
        }
        return (int)$value;
    }
}
