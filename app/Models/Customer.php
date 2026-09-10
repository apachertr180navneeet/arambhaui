<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class);
    }
}
