<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FaqItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FaqController extends Controller
{
    public function index(): View
    {
        return view('admin.faq.index', ['items' => FaqItem::ordered()->get()->groupBy('category')]);
    }

    public function create(): View
    {
        return view('admin.faq.create', [
            'categories' => FaqItem::select('category')->distinct()->orderBy('category')->pluck('category'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $data['sort_order'] = $data['sort_order'] ?? ((int) FaqItem::where('category', $data['category'])->max('sort_order') + 1);

        FaqItem::create($data);

        return redirect()->route('admin.faq.index')->with('status', 'faq-created');
    }

    public function edit(FaqItem $faqItem): View
    {
        return view('admin.faq.edit', ['item' => $faqItem]);
    }

    public function update(Request $request, FaqItem $faqItem): RedirectResponse
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:255'],
            'question' => ['required', 'string', 'max:255'],
            'answer' => ['required', 'string'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $faqItem->update($data);

        return redirect()->route('admin.faq.index')->with('status', 'faq-updated');
    }

    public function destroy(FaqItem $faqItem): RedirectResponse
    {
        $faqItem->delete();

        return redirect()->route('admin.faq.index')->with('status', 'faq-deleted');
    }
}
