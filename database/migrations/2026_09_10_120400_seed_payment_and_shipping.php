<?php

use App\Models\PaymentMethod;
use App\Models\ShippingOption;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Options de départ pour que la boutique soit utilisable dès l'activation
     * — insère seulement si les tables sont vides, pour ne jamais écraser une
     * configuration déjà personnalisée par l'admin.
     */
    public function up(): void
    {
        if (PaymentMethod::count() === 0) {
            PaymentMethod::create([
                'label' => 'Virement bancaire',
                'instructions' => "Un RIB vous sera envoyé par e-mail après validation de la commande. La commande est préparée dès réception du virement.",
                'enabled' => true,
                'sort_order' => 0,
            ]);
        }

        if (ShippingOption::count() === 0) {
            ShippingOption::create([
                'label' => "Retrait à l'atelier",
                'price_cents' => 0,
                'enabled' => true,
                'sort_order' => 0,
            ]);
            ShippingOption::create([
                'label' => 'Envoi postal (Colissimo)',
                'price_cents' => 690,
                'enabled' => true,
                'sort_order' => 1,
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
