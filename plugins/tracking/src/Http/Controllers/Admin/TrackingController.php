<?php

namespace App\Plugins\Tracking\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Plugins\Tracking\Models\TrackingVisit;
use Illuminate\View\View;

class TrackingController extends Controller
{
    public function index(): View
    {
        return view('plugins.tracking.admin.index', [
            'recent' => TrackingVisit::latest('created_at')->limit(100)->get(),
            'byDevice' => TrackingVisit::selectRaw('device_type, count(*) as total')
                ->groupBy('device_type')
                ->pluck('total', 'device_type'),
            'total' => TrackingVisit::count(),
        ]);
    }
}
