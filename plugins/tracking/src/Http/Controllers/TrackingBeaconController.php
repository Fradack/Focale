<?php

namespace App\Plugins\Tracking\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Plugins\Tracking\IpGeolocator;
use App\Plugins\Tracking\Models\TrackingVisit;
use App\Plugins\Tracking\Support\UserAgentParser;
use App\Support\Plugins;
use App\Support\Visitor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TrackingBeaconController extends Controller
{
    public function store(Request $request, IpGeolocator $geo): JsonResponse
    {
        // Double vérification : le plugin peut avoir été désactivé après le
        // chargement de la page, et sendBeacon() ne peut de toute façon pas
        // envoyer de cookie de consentement autrement que via ce cookie.
        if (! Plugins::enabled('tracking') || $request->cookie('focale_consent') !== 'accepted') {
            return response()->json(['ok' => false], 204);
        }

        $ip = $request->ip();
        $location = $geo->locate($ip) ?? [];

        TrackingVisit::create([
            'visitor_id' => Visitor::id(),
            'path' => (string) $request->input('path'),
            'ip' => $ip,
            'commune' => $location['commune'] ?? null,
            'country' => $location['country'] ?? null,
            'country_code' => $location['country_code'] ?? null,
            'device_type' => UserAgentParser::deviceType($request->userAgent()),
            'os' => UserAgentParser::os($request->userAgent()),
            'duration_seconds' => (int) $request->input('duration', 0),
        ]);

        return response()->json(['ok' => true]);
    }
}
