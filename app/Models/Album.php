<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;

class Album extends Model
{
    use HasFactory;

    protected $fillable = [
        'title', 'slug', 'cover_media_id', 'intro_text', 'external_link',
        'credits_text', 'created_on', 'published_at', 'status', 'visibility',
        'password_hash', 'layout_type', 'seo_auto', 'seo_title',
        'seo_description', 'seo_image_id', 'comments_enabled', 'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'created_on' => 'date',
            'published_at' => 'datetime',
            'seo_auto' => 'boolean',
            'comments_enabled' => 'boolean',
            'is_featured' => 'boolean',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function cover(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'cover_media_id');
    }

    public function seoImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'seo_image_id');
    }

    public function media(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'album_media')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function approvedComments(): HasMany
    {
        return $this->comments()->where('status', 'approved')->latest();
    }

    public function focaleCollections(): BelongsToMany
    {
        return $this->belongsToMany(FocaleCollection::class, 'collection_album')
            ->withPivot('sort_order')
            ->orderByPivot('sort_order');
    }

    public function setPassword(?string $password): void
    {
        $this->password_hash = $password ? Hash::make($password) : null;
    }

    public function checkPassword(string $password): bool
    {
        return $this->password_hash && Hash::check($password, $this->password_hash);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }

    /**
     * Albums listables publiquement (accueil, liste des albums, sitemap) :
     * publiés et jamais privés (les protégés par mot de passe restent listés,
     * seul leur contenu est verrouillé).
     */
    public function scopeListable($query)
    {
        return $query->published()->where('visibility', '!=', 'private');
    }
}
