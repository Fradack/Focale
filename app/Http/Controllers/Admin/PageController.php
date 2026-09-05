<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class PageController extends Controller
{
    public function index(): View
    {
        return view('admin.pages.index', ['pages' => Page::orderBy('title')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $title = trim((string) $request->input('title')) ?: 'Nouvelle page';
        $slug = Str::slug($title) ?: 'page';
        $base = $slug;
        $i = 1;
        while (Page::where('slug', $slug)->exists()) {
            $slug = $base.'-'.(++$i);
        }

        $page = Page::create(['title' => $title, 'slug' => $slug, 'template' => 'default', 'status' => 'draft']);

        return redirect()->route('admin.pages.edit', $page);
    }

    public function edit(Page $page): View
    {
        return view('admin.pages.edit', [
            'page' => $page,
            'block' => $page->blocks()->first(),
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash', 'unique:pages,slug,'.$page->id],
            'status' => ['required', 'in:draft,published'],
            'content' => ['nullable', 'string'],
            'seo_title' => ['nullable', 'string', 'max:255'],
            'seo_description' => ['nullable', 'string', 'max:500'],
        ]);

        $page->update([
            'title' => $data['title'],
            'slug' => $data['slug'],
            'status' => $data['status'],
            'seo_title' => $data['seo_title'] ?? null,
            'seo_description' => $data['seo_description'] ?? null,
        ]);

        $block = $page->blocks()->first();
        if ($block) {
            $block->update(['content' => ['text' => $data['content'] ?? '']]);
        } else {
            $page->blocks()->create(['type' => 'text', 'content' => ['text' => $data['content'] ?? ''], 'sort_order' => 0]);
        }

        return redirect()->route('admin.pages.edit', $page)->with('status', 'page-updated');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();

        return redirect()->route('admin.pages.index')->with('status', 'page-deleted');
    }
}
