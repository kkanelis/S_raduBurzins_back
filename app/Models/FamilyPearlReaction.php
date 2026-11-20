<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyPearlReaction extends Model
{
    protected $fillable = [
        'pearl_id',
        'user_id',
        'emoji',
    ];

    public function pearl(): BelongsTo
    {
        return $this->belongsTo(FamilyPearl::class, 'pearl_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
