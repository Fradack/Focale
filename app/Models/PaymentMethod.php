<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    public const TYPES = [
        'manual' => 'Manuel (virement, chèque, espèces…)',
        'paypal' => 'PayPal.me',
    ];

    protected $fillable = ['label', 'type', 'paypal_link', 'instructions', 'enabled', 'sort_order'];

    protected function casts(): array
    {
        return ['enabled' => 'boolean'];
    }

    public function isPaypal(): bool
    {
        return $this->type === 'paypal';
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true)->orderBy('sort_order');
    }

    /**
     * Instructions finales affichées au client pour une commande donnée. Pour
     * PayPal.me, injecte le lien et le code de commande à transmettre dans le
     * message du paiement — indispensable puisque PayPal.me ne permet pas de
     * préremplir cette référence dans l'URL, c'est au client de la recopier.
     */
    public function buildInstructions(string $orderNumber, string $totalFormatted): ?string
    {
        if (! $this->isPaypal()) {
            return $this->instructions;
        }

        $paypal = trim(
            "Réglez {$totalFormatted} via PayPal.me : {$this->paypal_link}\n\n"
            ."Important : indiquez le code de commande {$orderNumber} dans le champ « Pour » / message du paiement PayPal, sinon nous ne pourrons pas identifier à qui appartient le règlement."
        );

        return $this->instructions ? $paypal."\n\n".$this->instructions : $paypal;
    }
}
