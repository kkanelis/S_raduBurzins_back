<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FamilyPearlMedia extends Model
{
    protected $table = 'family_pearl_media';

    protected $fillable = [
        'pearl_id',
        'type',
        'path',
        'disk',
        'mime_type',
        'size',
        'sort_order',
    ];

    protected $casts = ['size' => 'integer'];

    public function pearl(): BelongsTo
    {
        return $this->belongsTo(FamilyPearl::class, 'pearl_id');
    }
}
