<?php

namespace App\Plugins\Avis\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReviewAnswer extends Model
{
    protected $fillable = ['review_id', 'review_question_id', 'question_text', 'answer_text', 'answer_rating'];

    public function review(): BelongsTo
    {
        return $this->belongsTo(Review::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ReviewQuestion::class, 'review_question_id');
    }
}
