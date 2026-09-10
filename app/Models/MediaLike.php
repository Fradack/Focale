<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MediaLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['media_id', 'visitor_id'];
}
