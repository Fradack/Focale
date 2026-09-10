<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlbumLike;
use App\Models\MediaLike;
use App\Models\Plugin;
use Illuminate\View\View;

class LikeSettingController extends Controller
{
    public function index(): View
    {
        return view('admin.likes.index', [
            'plugin' => Plugin::findOrFail('likes'),
            'mediaLikes' => MediaLike::count(),
            'albumLikes' => AlbumLike::count(),
        ]);
    }
}
