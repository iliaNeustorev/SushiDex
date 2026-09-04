<?php

namespace App\Models;

use App\Enums\Posts\Status;
use App\Traits\HasImages;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use HasFactory, HasImages, SoftDeletes;

    protected $fillable = [
        'url',
        'content',
        'title',
        'category_id',
        'status',
        'user_id',
    ];

    protected $casts = [
        'status' => Status::class,
    ];

    public function getCreatedAtFormattedAttribute(): string
    {
        return $this->created_at->format('d.m.Y H:i:s');
    }

    public function getUpdatedAtFormattedAttribute(): string
    {
        return $this->updated_at->format('d.m.Y H:i:s');
    }

    public function getIsDraftAttribute(): bool
    {
        return $this->status === Status::DRAFT;
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * @param [type] $query
     * @return [type] $query
     */
    public function scopeFilter($query, array $filters = [])
    {
        return $query
            ->when(isset($filters['status']), fn($query) => $query->where('status', $filters['status']))
            ->when(isset($filters['date']), fn($query) => $query->whereDate('created_at', '>=', $filters['date']))
            ->when(isset($filters['category']), fn($query) => $query->where('category_id', $filters['category']));
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function scopeByUserId($query, ?int $userId)
    {
        return $query->when(isset($userId), fn($query) => $query->where('user_id', $userId));
    }
}
