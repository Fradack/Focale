<?php

use App\Plugins\Tracking\Http\Controllers\Admin\TrackingController;
use App\Plugins\Tracking\Http\Controllers\TrackingBeaconController;
use Illuminate\Support\Facades\Route;

if (! \App\Support\Plugins::enabled('tracking')) {
    return;
}

Route::prefix('administration')->name('admin.')->middleware(['auth', 'staff'])->group(function () {
    Route::get('tracking', [TrackingController::class, 'index'])->name('tracking.index');
});

Route::middleware('maintenance')->post('/tracking/vue', [TrackingBeaconController::class, 'store'])
    ->name('public.tracking.beacon');
