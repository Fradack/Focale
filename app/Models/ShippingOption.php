<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingOption extends Model
{
    protected $fillable = ['label', 'price_cents', 'enabled', 'sort_order'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }

    public function priceFormatted(): string
    {
        return $this->price_cents > 0
            ? number_format($this->price_cents / 100, 2, ',', ' ').' €'
            : 'Gratuit';
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true)->orderBy('sort_order');
    }
}
