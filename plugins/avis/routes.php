<?php

use App\Plugins\Avis\Http\Controllers\Admin\ReviewController as AdminReviewController;
use App\Plugins\Avis\Http\Controllers\Admin\ReviewQuestionController;
use App\Plugins\Avis\Http\Controllers\ReviewController;
use Illuminate\Support\Facades\Route;

if (! \App\Support\Plugins::enabled('avis')) {
    return;
}

Route::middleware('maintenance')->group(function () {
    Route::get('/avis', [ReviewController::class, 'create'])->name('public.avis.create');
    Route::post('/avis', [ReviewController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('public.avis.store');
});

Route::prefix('administration')->name('admin.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('avis', [AdminReviewController::class, 'index'])->name('avis.index');

    Route::get('avis/questions', [ReviewQuestionController::class, 'index'])->name('avis.questions.index');
    Route::post('avis/questions', [ReviewQuestionController::class, 'store'])->name('avis.questions.store');
    Route::put('avis/questions/{question}', [ReviewQuestionController::class, 'update'])->name('avis.questions.update');
    Route::delete('avis/questions/{question}', [ReviewQuestionController::class, 'destroy'])->name('avis.questions.destroy');
});
