<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Page extends Model
{
    protected $fillable = ['title', 'slug', 'template', 'status', 'show_in_nav', 'seo_title', 'seo_description', 'seo_image_id'];

    protected function casts(): array
    {
        return [
            'show_in_nav' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function seoImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'seo_image_id');
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class)->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeInNav($query)
    {
        return $query->published()->where('show_in_nav', true);
    }
}
