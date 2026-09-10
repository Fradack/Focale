<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlbumLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['album_id', 'visitor_id'];

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}
