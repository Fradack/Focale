<?php

return [

    // Version installée localement. Comparée au dernier tag GitHub par le
    // système de mise à jour — mise à jour automatiquement après une MAJ.
    'version' => '26.37.9b',

    // Dépôt GitHub source des releases (App\Services\UpdateService).
    'update_repo' => 'Fradack/Focale',

    // Chemin du binaire mysqldump, variable selon l'hébergement.
    'mysqldump_path' => env('MYSQLDUMP_PATH', 'mysqldump'),

];
