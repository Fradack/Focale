<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    public $timestamps = false;

    protected $fillable = ['visitor_id', 'path', 'referrer', 'visited_on'];

    protected function casts(): array
    {
        return ['visited_on' => 'date'];
    }
}
