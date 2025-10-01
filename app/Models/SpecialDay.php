<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpecialDay extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'date',
        'repeats'
    ];

    protected $casts = [
        'date' => 'date',
        'repeats' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}