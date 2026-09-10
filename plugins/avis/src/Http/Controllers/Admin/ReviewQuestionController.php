<?php

namespace App\Plugins\Avis\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plugins\Avis\Models\ReviewQuestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ReviewQuestionController extends Controller
{
    public function index(): View
    {
        return view('plugins.avis.admin.questions.index', [
            'questions' => ReviewQuestion::orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(ReviewQuestion::TYPES))],
            'sort_order' => ['nullable', 'integer'],
        ]);

        ReviewQuestion::create([
            'question' => $data['question'],
            'type' => $data['type'],
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => true,
        ]);

        return back()->with('status', 'question-created');
    }

    public function update(Request $request, ReviewQuestion $question): RedirectResponse
    {
        $data = $request->validate([
            'question' => ['required', 'string', 'max:255'],
            'type' => ['required', Rule::in(array_keys(ReviewQuestion::TYPES))],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $question->update([
            'question' => $data['question'],
            'type' => $data['type'],
            'sort_order' => $data['sort_order'] ?? 0,
            'active' => $request->boolean('active'),
        ]);

        return back()->with('status', 'question-updated');
    }

    public function destroy(ReviewQuestion $question): RedirectResponse
    {
        $question->delete();

        return back()->with('status', 'question-deleted');
    }
}
