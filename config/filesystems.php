<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        // Fichiers originaux des œuvres : hors webroot, jamais servis directement
        // (brief section 12). Les variantes publiques (thumbnail/web/retina)
        // vivent sur le disque "public" ci-dessous.
        'media' => [
            'driver' => 'local',
            'root' => storage_path('app/media'),
            'serve' => false,
            'throw' => true,
            'report' => false,
        ],

        // Racine placée directement dans public_path() (et non storage_path()) :
        // les variantes publiques sont donc servies sans lien symbolique, ce qui
        // fonctionne aussi sur les hébergements mutualisés en FTP seul, sans
        // accès SSH pour lancer `artisan storage:link`.
        'public' => [
            'driver' => 'local',
            'root' => public_path('storage'),
            'url' => rtrim(env('APP_URL', 'http://localhost'), '/').'/storage',
            'visibility' => 'public',
            'throw' => true,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    // Aucun lien symbolique nécessaire : le disque "public" ci-dessus écrit
    // déjà directement dans public_path('storage').
    'links' => [],

];
