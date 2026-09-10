<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FaqItem extends Model
{
    protected $fillable = ['category', 'audience', 'question', 'answer', 'sort_order'];

    public function scopeOrdered($query)
    {
        return $query->orderBy('category')->orderBy('sort_order');
    }

    public function scopeVisitor($query)
    {
        return $query->where('audience', 'visitor');
    }

    public function scopeAdmin($query)
    {
        return $query->where('audience', 'admin');
    }
}
