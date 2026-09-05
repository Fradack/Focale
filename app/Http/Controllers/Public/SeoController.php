<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Page;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function sitemap(): Response
    {
        $urls = collect([
            ['loc' => url('/'), 'lastmod' => now()->toAtomString()],
            ['loc' => route('public.albums'), 'lastmod' => now()->toAtomString()],
        ]);

        Album::listable()->where('visibility', 'public')->get()
            ->each(function (Album $album) use ($urls) {
                $urls->push(['loc' => route('public.album', $album), 'lastmod' => $album->updated_at->toAtomString()]);
            });

        Page::where('status', 'published')->get()
            ->each(function (Page $page) use ($urls) {
                $urls->push(['loc' => route('page.show', $page), 'lastmod' => $page->updated_at->toAtomString()]);
            });

        $xml = view('public.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }

    public function robots(): Response
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Disallow: /administration',
            'Sitemap: '.route('public.sitemap'),
        ];

        return response(implode("\n", $lines), 200)->header('Content-Type', 'text/plain');
    }
}
