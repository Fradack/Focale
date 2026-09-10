{{-- Palette sombre partagée par toutes les pages publiques : chaque page
     garde son propre bloc `:root { ... }` (les valeurs claires varient
     légèrement d'une page à l'autre) mais inclut ce partiel juste après pour
     ajouter le pendant sombre, sans dupliquer 9 fois les mêmes valeurs.
     Variables inutilisées par une page donnée : sans effet, pas d'erreur.
     Même caractère chaleureux que la palette claire (pas de noir/blanc pur),
     vérifié AA (≥4.5:1) texte sur fond et texte sur panneau. --}}
<style>
:root[data-theme="dark"] {
  --bg: #1B1815;
  --panel: #242019;
  --ink: #EDE9E2;
  --ink-soft: #B4AC9C;
  /* #8C5A3D, pas un ton plus clair : --clay sert de fond plein avec texte
     blanc dessus (boutons .slideshow-btn.active, .comment-form button,
     bouton de contact) — doit rester assez sombre pour ≥4.5:1 avec du blanc.
     Un ton plus clair suffirait pour du texte clay sur fond sombre, mais
     aucune page publique ne s'en sert ainsi actuellement. */
  --clay: #8C5A3D;
  --line: #423B32;
  --img-fallback: #332E27;
  /* Pour les pastilles "actives" qui utilisent var(--ink) comme FOND (pas
     comme texte) avec du blanc dessus (ex: .pagination-link.active dans
     albums.blade.php / gallery.blade.php) — --ink devenant clair en sombre,
     réutiliser var(--ink) comme fond casserait le contraste. --active-bg/
     --active-text gardent le même traitement "inversé, le plus contrasté"
     dans les deux thèmes. */
  --active-bg: #EDE9E2;
  --active-text: #1E1C19;
  /* Rouge d'erreur de formulaire (contact.blade.php, album.blade.php) : les
     pages ne le déclarent qu'en littéral (#A3402E, ~2.8:1 sur fond sombre,
     échoue l'AA) — même mécanisme de repli var(--danger, #A3402E) que pour
     --active-bg/--active-text ci-dessus, pas besoin de toucher leur :root. */
  --danger: #E2796A;
  /* Fond de bloc <pre> (page.blade.php) et de champs de formulaire admin :
     même repli var(--field-bg, #fff) que ci-dessus, pas de :root à toucher. */
  --field-bg: #2C2620;
}
</style>
