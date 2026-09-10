<?php

namespace App\Plugins\Avis\Models;

use Illuminate\Database\Eloquent\Model;

class ReviewQuestion extends Model
{
    public const TYPES = [
        'text' => 'Réponse libre',
        'rating' => 'Note (1 à 5)',
    ];

    protected $fillable = ['question', 'type', 'sort_order', 'active'];

    protected function casts(): array
    {
        return ['active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }
}
