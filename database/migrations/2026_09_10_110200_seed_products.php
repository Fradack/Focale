<?php

use App\Models\Product;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Catalogue de départ pour la boutique — insère seulement si la table est
     * vide, pour ne jamais écraser un catalogue déjà personnalisé par l'admin.
     */
    private const PRODUCTS = [
        [
            'title' => 'Album photo relié',
            'type' => 'album',
            'description' => "Un album relié imprimé sur papier photo mat, jusqu'à 40 pages. Idéal pour rassembler une série ou un reportage complet.",
            'variants' => [
                ['label' => '20×20 cm, 20 pages', 'price_cents' => 4900],
                ['label' => '30×30 cm, 30 pages', 'price_cents' => 7900],
                ['label' => '30×30 cm, 40 pages', 'price_cents' => 9900],
            ],
        ],
        [
            'title' => 'Album photo souple',
            'type' => 'album',
            'description' => "Version souple et légère de l'album relié, même qualité d'impression, format plus compact.",
            'variants' => [
                ['label' => '15×20 cm, 20 pages', 'price_cents' => 2900],
                ['label' => '20×30 cm, 20 pages', 'price_cents' => 3900],
            ],
        ],
        [
            'title' => 'Tirage encadré',
            'type' => 'cadre',
            'description' => 'Un tirage sur papier photo premium, encadré et prêt à accrocher.',
            'variants' => [
                ['label' => '20×30 cm, cadre noir', 'price_cents' => 5900],
                ['label' => '30×40 cm, cadre noir', 'price_cents' => 8900],
                ['label' => '30×40 cm, cadre bois clair', 'price_cents' => 8900],
                ['label' => '40×60 cm, cadre noir', 'price_cents' => 12900],
            ],
        ],
        [
            'title' => 'Caisse américaine',
            'type' => 'cadre',
            'description' => "Tirage encadré sous caisse américaine, avec marge d'air entre l'image et le verre.",
            'variants' => [
                ['label' => '30×40 cm', 'price_cents' => 9900],
                ['label' => '40×60 cm', 'price_cents' => 14900],
            ],
        ],
        [
            'title' => 'Tableau toile',
            'type' => 'tableau',
            'description' => 'Impression sur toile de coton tendue sur châssis en bois, sans vitre ni reflet.',
            'variants' => [
                ['label' => '30×40 cm', 'price_cents' => 6900],
                ['label' => '40×60 cm', 'price_cents' => 9900],
                ['label' => '60×90 cm', 'price_cents' => 15900],
            ],
        ],
    ];

    public function up(): void
    {
        if (Product::count() > 0) {
            return;
        }

        foreach (self::PRODUCTS as $i => $data) {
            $product = Product::create([
                'title' => $data['title'],
                'slug' => str($data['title'])->slug(),
                'type' => $data['type'],
                'description' => $data['description'],
                'status' => 'published',
                'sort_order' => $i,
            ]);

            foreach ($data['variants'] as $j => $variant) {
                $product->variants()->create($variant + ['sort_order' => $j]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
