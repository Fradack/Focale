<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FocaleCollection extends Model
{
    protected $table = 'focale_collections';

    protected $fillable = ['title', 'slug', 'intro_text', 'cover_media_id', 'seo_title', 'seo_description'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'collection_album')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }
}
