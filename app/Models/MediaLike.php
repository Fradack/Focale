<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['media_id', 'visitor_id'];

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class);
    }
}
