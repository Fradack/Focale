<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Traite la file d'attente des variantes de photos (GenerateMediaVariants).
// Sur un hébergement mutualisé Plesk, il n'y a pas de worker `queue:work`
// permanent : cette tâche vide la file toutes les minutes à la place.
Schedule::command('queue:work --stop-when-empty --max-time=50 --tries=3')
    ->everyMinute()
    ->withoutOverlapping();
