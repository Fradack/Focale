<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Focale n'a pas de compte de démonstration : l'administrateur principal
     * est créé via l'assistant d'installation (/installation), jamais par un
     * seeder — voir App\Http\Controllers\Install\InstallController.
     */
    public function run(): void
    {
        //
    }
}
