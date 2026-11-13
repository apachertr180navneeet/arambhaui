<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'transaction_date' => 'date',
        'debit' => 'float',
        'credit' => 'float',
        'qty_in' => 'float',
        'qty_out' => 'float',
        'running_balance' => 'float',
    ];

    /**
     * Item name accessor (mapping to entity_name for ItemStock transactions)
     */
    public function getItemNameAttribute(): string
    {
        return $this->attributes['entity_name'] ?? '';
    }

    /**
     * Transaction type alias for blade templates ($t->type)
     */
    public function getTypeAttribute(): string
    {
        return $this->attributes['transaction_type'] ?? '';
    }

    /**
     * Inward quantity accessor ($t->qty_in)
     */
    public function getQtyInAttribute(): float
    {
        if (isset($this->attributes['qty_in']) && (float)$this->attributes['qty_in'] > 0) {
            return (float)$this->attributes['qty_in'];
        }
        return (float)($this->attributes['credit'] ?? 0);
    }

    /**
     * Outward quantity accessor ($t->qty_out)
     */
    public function getQtyOutAttribute(): float
    {
        if (isset($this->attributes['qty_out']) && (float)$this->attributes['qty_out'] > 0) {
            return (float)$this->attributes['qty_out'];
        }
        return (float)($this->attributes['debit'] ?? 0);
    }

    /**
     * Relation to Item (for ItemStock ledgers)
     */
    public function item()
    {
        return $this->belongsTo(Item::class, 'entity_id');
    }
}
