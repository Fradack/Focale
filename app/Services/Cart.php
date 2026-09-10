<?php

namespace App\Services;

use App\Models\ProductVariant;
use Illuminate\Support\Collection;

/**
 * Panier client, stocké en session (pas de commande/paiement pour l'instant —
 * voir Documents/PROJET.md, "Vente de tirages" reste hors MVP mais la
 * navigation boutique/panier prépare le terrain).
 */
class Cart
{
    private const SESSION_KEY = 'cart';

    public function add(int $variantId, int $quantity = 1): void
    {
        $items = $this->raw();
        $items[$variantId] = ($items[$variantId] ?? 0) + max(1, $quantity);
        session()->put(self::SESSION_KEY, $items);
    }

    public function update(int $variantId, int $quantity): void
    {
        $items = $this->raw();

        if ($quantity < 1) {
            unset($items[$variantId]);
        } else {
            $items[$variantId] = $quantity;
        }

        session()->put(self::SESSION_KEY, $items);
    }

    public function remove(int $variantId): void
    {
        $this->update($variantId, 0);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    /**
     * @return Collection<int, array{variant: ProductVariant, quantity: int, subtotal_cents: int}>
     */
    public function items(): Collection
    {
        $raw = $this->raw();

        if (empty($raw)) {
            return collect();
        }

        return ProductVariant::with('product.coverMedia.variants')
            ->whereIn('id', array_keys($raw))
            ->get()
            ->map(fn (ProductVariant $variant) => [
                'variant' => $variant,
                'quantity' => $raw[$variant->id],
                'subtotal_cents' => $variant->price_cents * $raw[$variant->id],
            ]);
    }

    public function totalCents(): int
    {
        return (int) $this->items()->sum('subtotal_cents');
    }

    public function totalFormatted(): string
    {
        return number_format($this->totalCents() / 100, 2, ',', ' ').' €';
    }

    public function count(): int
    {
        return array_sum($this->raw());
    }

    private function raw(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }
}
