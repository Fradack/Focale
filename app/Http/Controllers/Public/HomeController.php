<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Media;
use App\Models\Setting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        $coverMediaId = Setting::get('home_cover_media_id');

        $featured = Album::listable()->where('is_featured', true)
            ->with('cover.variants')
            ->latest('published_at')
            ->limit(6)
            ->get();

        if ($featured->isEmpty()) {
            $featured = Album::listable()->with('cover.variants')->latest('published_at')->limit(6)->get();
        }

        return view('public.home', [
            'cover' => $coverMediaId ? Media::find($coverMediaId) : null,
            'artistName' => Setting::get('artist_name'),
            'bio' => Setting::get('bio'),
            'featured' => $featured,
            'aboutPage' => \App\Models\Page::where('slug', 'a-propos')->where('status', 'published')->first(),
        ]);
    }
}
