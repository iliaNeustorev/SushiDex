<?php

namespace App\Models;

use App\Enums\Orders\Status;
use App\Enums\Orders\TypePaid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    protected $guarded = [];

    protected $casts = [
        'status' => Status::class,
        'total_price' => 'decimal:2',
        'type_paid' => TypePaid::class,
        'completed_at' => 'datetime',
    ];

    public const LIMIT_ACTUAL_ORDERS = 2;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_items')
            ->withPivot(['id', 'count', 'price'])
            ->withTimestamps();
    }

    public function scopeByUserId($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeActualOrder($query, int $userId)
    {
        return $query->byUserId($userId)->actualStatus();
    }

    public function scopeByStatus($query, array $statuses)
    {
        return $query->whereIn('status', $statuses);
    }

    public function scopeActualStatus($query)
    {
        return $query->byStatus([Status::NEW, Status::PROCESSING]);
    }

    public function scopeCompletedStatus($query)
    {
        return $query->byStatus([Status::CANCELLED, Status::COMPLETED]);
    }

    public function remittances(): HasMany
    {
        return $this->hasMany(Remittance::class);
    }

    public function remittance(): HasOne
    {
        return $this->hasOne(Remittance::class)->latestOfMany();
    }
}
