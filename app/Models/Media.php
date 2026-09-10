<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

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
            'taken_at' => 'datetime',
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

    /**
     * Lieu à afficher publiquement : le texte saisi par l'artiste, ou à
     * défaut les coordonnées GPS lues dans l'EXIF — le tout masqué si
     * `hide_gps` est actif (ce réglage cache la localisation dans son
     * ensemble, pas seulement les coordonnées brutes).
     */
    public function displayLocation(): ?string
    {
        if ($this->hide_gps) {
            return null;
        }

        return $this->location ?: $this->gpsCoordinatesLabel();
    }

    public function gpsCoordinatesLabel(): ?string
    {
        if ($this->gps_lat === null || $this->gps_lng === null) {
            return null;
        }

        return number_format((float) $this->gps_lat, 5).', '.number_format((float) $this->gps_lng, 5);
    }

    /**
     * Lien vers une carte (OpenStreetMap) centrée sur les coordonnées GPS —
     * aucun service de géocodage externe : pas de nom de lieu résolu, juste
     * un point sur la carte.
     */
    public function mapUrl(): ?string
    {
        if ($this->hide_gps || $this->gps_lat === null || $this->gps_lng === null) {
            return null;
        }

        return "https://www.openstreetmap.org/?mlat={$this->gps_lat}&mlon={$this->gps_lng}#map=15/{$this->gps_lat}/{$this->gps_lng}";
    }

    public function likes(): HasMany
    {
        return $this->hasMany(MediaLike::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(MediaView::class);
    }

    public function isLikedBy(string $visitorId): bool
    {
        return $this->likes()->where('visitor_id', $visitorId)->exists();
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

    public function isVideo(): bool
    {
        return str_starts_with((string) $this->mime_type, 'video/');
    }

    /**
     * URL jouable/affichable de l'œuvre : le fichier vidéo lui-même (stocké
     * directement sur le disque public — voir MediaIngestService), ou la
     * variante "web" pour une image. Les lignes de variantes créées pour une
     * vidéo ne servent qu'à satisfaire MediaProcessingStatus (voir
     * MediaIngestService::ingest()) et ne doivent jamais être utilisées comme
     * source d'affichage.
     */
    public function sourceUrl(): ?string
    {
        return $this->isVideo()
            ? Storage::disk('public')->url($this->disk_path)
            : $this->variant('web')?->url();
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
