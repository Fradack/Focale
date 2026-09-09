<?php

namespace App\Services;

use Illuminate\Queue\WorkerOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * Sur un hébergement mutualisé sans accès SSH, il n'y a aucun moyen de garder
 * un `artisan queue:work` actif en permanence (pas de tâche planifiée
 * garantie non plus). Plutôt que de laisser les imports s'accumuler sans
 * jamais être traités, chaque consultation du statut de traitement (voir
 * MediaProcessingStatus, sondé par la bannière admin et par les pages
 * d'album publiques) traite elle-même quelques jobs en attente. Le simple
 * fait de garder un onglet ouvert fait donc avancer la file, sans
 * configuration serveur particulière.
 */
class QueuePump
{
    private const MAX_JOBS = 15;

    private const MAX_SECONDS = 20;

    public static function pumpIfDue(): void
    {
        if (config('queue.default') !== 'database') {
            return;
        }

        // Un seul "coup de pompe" à la fois, même si plusieurs onglets sondent en parallèle.
        if (! Cache::add('queue-pump-lock', true, 2)) {
            return;
        }

        $worker = app('queue.worker');
        $options = new WorkerOptions(maxTries: 1, timeout: 25, sleep: 0);

        $deadline = microtime(true) + self::MAX_SECONDS;
        $processed = 0;

        while ($processed < self::MAX_JOBS && microtime(true) < $deadline) {
            if (DB::table('jobs')->count() === 0) {
                break;
            }

            $worker->runNextJob('database', 'default', $options);
            $processed++;
        }
    }
}
