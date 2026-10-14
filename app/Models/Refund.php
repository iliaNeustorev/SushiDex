<?php

namespace App\Models;

use App\Enums\Refunds\Status;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Refund extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'amount' => 'decimal:2',
        'refunded_at' => 'datetime',
    ];

    public function remittance(): BelongsTo
    {
        return $this->belongsTo(Remittance::class);
    }
}
