<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FamilyPearl extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'category_id',
        'title',
        'text',
        'context',
        'is_anonymous',
        'status',
        'moderation_note',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'is_anonymous' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(FamilyPearlCategory::class, 'category_id');
    }

    public function media(): HasMany
    {
        return $this->hasMany(FamilyPearlMedia::class, 'pearl_id')->orderBy('sort_order');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(FamilyPearlReaction::class, 'pearl_id');
    }
}
