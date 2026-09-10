<?php

use App\Models\FaqItem;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * "Une photo à la fois" n'est plus un réglage optionnel (voir la
     * suppression de import_one_by_one/import_concurrency) : la question de
     * la FAQ Administrateur qui en parlait doit refléter que c'est
     * maintenant le comportement systématique, plus un choix.
     */
    private const OLD_QUESTION = 'À quoi sert le réglage "Import une photo par une photo" ?';

    private const NEW_QUESTION = 'Pourquoi les photos s\'importent une par une, sans réglage pour le changer ?';

    private const NEW_ANSWER = "Chaque photo doit être entièrement envoyée ET traitée à 100% avant que la suivante ne démarre — y compris pour l'import depuis un dossier serveur (les fichiers sont déjà sur le serveur, mais leur traitement reste séquentiel). Ce n'est plus un réglage optionnel : une seule photo trop lourde ne peut ainsi jamais bloquer toute une file de dizaines d'autres derrière elle.";

    public function up(): void
    {
        FaqItem::where('question', self::OLD_QUESTION)->update([
            'question' => self::NEW_QUESTION,
            'answer' => self::NEW_ANSWER,
        ]);
    }

    public function down(): void
    {
        //
    }
};
