<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaView extends Model
{
    public $timestamps = false;

    protected $fillable = ['media_id', 'visitor_id', 'viewed_on'];

    protected function casts(): array
    {
        return ['viewed_on' => 'date'];
    }
}
