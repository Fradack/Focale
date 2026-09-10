<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlbumLike extends Model
{
    public $timestamps = false;

    protected $fillable = ['album_id', 'visitor_id'];
}
