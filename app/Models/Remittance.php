<?php

namespace App\Models;

use App\Enums\Remittances\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Remittance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'paid' => 'boolean',
        'amount' => 'decimal:2',
        'payed_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
