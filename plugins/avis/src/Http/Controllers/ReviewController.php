<?php

namespace App\Plugins\Avis\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Avis\Models\Review;
use App\Plugins\Avis\Models\ReviewQuestion;
use App\Services\SpamGuard;
use App\Support\Visitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function create(): View
    {
        return view('plugins.avis.public.create', [
            'questions' => ReviewQuestion::active()->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function store(Request $request, SpamGuard $spamGuard): RedirectResponse
    {
        $request->validate(['website' => ['prohibited']]);

        if ($spamGuard->looksAutomated($request)) {
            return back()->with('status', 'avis-envoye');
        }

        $questions = ReviewQuestion::active()->orderBy('sort_order')->orderBy('id')->get();
        $customer = $request->user()?->is_customer ? $request->user() : null;

        $review = Review::create([
            'visitor_id' => $customer ? null : Visitor::id(),
            'user_id' => $customer?->id,
            'submitted_at' => now(),
        ]);

        foreach ($questions as $question) {
            $raw = $request->input("answers.{$question->id}");

            if ($raw === null || $raw === '') {
                continue;
            }

            $review->answers()->create([
                'review_question_id' => $question->id,
                'question_text' => $question->question,
                'answer_text' => $question->type === 'text' ? Str::limit((string) $raw, 2000, '') : null,
                'answer_rating' => $question->type === 'rating' ? max(1, min(5, (int) $raw)) : null,
            ]);
        }

        return back()->with('status', 'avis-envoye');
    }
}
