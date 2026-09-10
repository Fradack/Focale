<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConsentController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $value = $request->input('consent') === 'accepted' ? 'accepted' : 'rejected';

        return response()->json(['ok' => true])
            ->withCookie(cookie('focale_consent', $value, 60 * 24 * 365));
    }
}
