<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Media;
use App\Services\PhotoEditor;
use App\Support\Plugins;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class PhotoEditController extends Controller
{
    public function edit(Media $media): View
    {
        abort_unless(Plugins::enabled('photoedit'), 404);
        abort_if($media->isVideo(), 404);

        return view('admin.media.edit-image', ['media' => $media]);
    }

    public function apply(Request $request, Media $media, PhotoEditor $editor): RedirectResponse
    {
        abort_unless(Plugins::enabled('photoedit'), 404);
        abort_if($media->isVideo(), 404);

        $data = $request->validate([
            'crop_x' => ['nullable', 'integer', 'min:0'],
            'crop_y' => ['nullable', 'integer', 'min:0'],
            'crop_width' => ['nullable', 'integer', 'min:1'],
            'crop_height' => ['nullable', 'integer', 'min:1'],
            'rotate' => ['nullable', 'integer'],
        ]);

        $crop = null;
        if (! empty($data['crop_width']) && ! empty($data['crop_height'])) {
            $crop = [
                'x' => (int) ($data['crop_x'] ?? 0),
                'y' => (int) ($data['crop_y'] ?? 0),
                'width' => (int) $data['crop_width'],
                'height' => (int) $data['crop_height'],
            ];
        }

        try {
            $editor->applyEdit($media, $crop, (int) ($data['rotate'] ?? 0));
        } catch (\Throwable $e) {
            return back()->withErrors(['photoedit' => "Modification échouée : {$e->getMessage()}"]);
        }

        return redirect()->route('admin.media.edit-image', $media)->with('status', 'Image modifiée.');
    }

    public function revert(Media $media, PhotoEditor $editor): RedirectResponse
    {
        abort_unless(Plugins::enabled('photoedit'), 404);
        abort_if($media->isVideo(), 404);

        try {
            $editor->revertToOriginal($media);
        } catch (\Throwable $e) {
            return back()->withErrors(['photoedit' => "Restauration échouée : {$e->getMessage()}"]);
        }

        return redirect()->route('admin.media.edit-image', $media)->with('status', 'Original restauré.');
    }

    /**
     * Sert l'original d'une œuvre à l'éditeur (Cropper.js) sans l'exposer
     * publiquement — le disque "media" est privé (jamais d'URL directe),
     * contrairement aux variantes sur le disque "public".
     */
    public function original(Media $media): Response
    {
        abort_unless(Plugins::enabled('photoedit'), 404);
        abort_if($media->isVideo(), 404);

        $disk = Storage::disk('media')->exists($media->disk_path) ? 'media' : 'public';

        return Storage::disk($disk)->response($media->disk_path);
    }
}
