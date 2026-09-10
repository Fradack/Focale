<?php

use App\Plugins\LivreDor\Http\Controllers\Admin\GuestbookController as AdminGuestbookController;
use App\Plugins\LivreDor\Http\Controllers\GuestbookController;
use Illuminate\Support\Facades\Route;

if (! \App\Support\Plugins::enabled('livre-dor')) {
    return;
}

Route::middleware('maintenance')->group(function () {
    Route::post('/album/{album:slug}/livre-dor', [GuestbookController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('public.guestbook.store');
});

Route::prefix('administration')->name('admin.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('livre-dor', [AdminGuestbookController::class, 'index'])->name('livre-dor.index');
    Route::patch('livre-dor/{entry}/approuver', [AdminGuestbookController::class, 'approve'])->name('livre-dor.approve');
    Route::delete('livre-dor/{entry}', [AdminGuestbookController::class, 'destroy'])->name('livre-dor.destroy');
});
