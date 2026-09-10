<?php

namespace App\Plugins\Tracking\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingVisit extends Model
{
    public $timestamps = false;

    protected $table = 'tracking_visits';

    protected $fillable = ['visitor_id', 'path', 'ip', 'commune', 'country', 'device_type', 'duration_seconds'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }
}
