<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Media extends Model
{
    use HasFactory;

    /**
     * Champs de la fiche technique (colonne `exif`) que l'artiste peut
     * masquer individuellement sur le site public — voir visibleExif().
     */
    public const EXIF_FIELDS = [
        'camera' => 'Appareil',
        'lens' => 'Objectif',
        'focal_length' => 'Focale',
        'aperture' => 'Ouverture',
        'shutter_speed' => 'Vitesse',
        'iso' => 'ISO',
    ];

    protected $fillable = [
        'uuid', 'title', 'slug', 'alt_text', 'caption', 'description',
        'credit', 'copyright', 'author', 'license', 'taken_at', 'location',
        'gps_lat', 'gps_lng', 'hide_gps', 'exif', 'exif_hidden_fields', 'mime_type', 'disk_path',
        'filesize', 'width', 'height', 'checksum', 'status', 'trashed_at',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'date',
            'trashed_at' => 'datetime',
            'hide_gps' => 'boolean',
            'exif' => 'array',
            'exif_hidden_fields' => 'array',
            'gps_lat' => 'decimal:7',
            'gps_lng' => 'decimal:7',
        ];
    }

    /**
     * Les champs EXIF à afficher publiquement : la fiche technique complète,
     * moins ceux que l'artiste a choisi de masquer pour cette œuvre.
     */
    public function visibleExif(): array
    {
        $hidden = $this->exif_hidden_fields ?? [];

        return array_diff_key($this->exif ?? [], array_flip($hidden));
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MediaVariant::class);
    }

    public function albums(): BelongsToMany
    {
        return $this->belongsToMany(Album::class, 'album_media')
            ->withPivot('sort_order')
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }

    /**
     * Retire cette œuvre de tous les albums qui la contiennent (mise à la
     * corbeille) en réassignant la couverture des albums concernés.
     */
    public function removeFromAlbums(): void
    {
        $albumIds = $this->albums()->pluck('albums.id');

        $this->albums()->detach();

        Album::whereIn('id', $albumIds)
            ->where('cover_media_id', $this->id)
            ->get()
            ->each(fn (Album $album) => $album->update(['cover_media_id' => $album->media()->first()?->id]));

        Album::where('seo_image_id', $this->id)->update(['seo_image_id' => null]);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'media_category');
    }

    public function variant(string $type): ?MediaVariant
    {
        return $this->variants->firstWhere('type', $type);
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published')->whereNull('trashed_at');
    }

    public function scopeTrashed($query)
    {
        return $query->whereNotNull('trashed_at');
    }
}
