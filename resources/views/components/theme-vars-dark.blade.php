{{-- Palettes sombre + saisonnières partagées par toutes les pages publiques :
     chaque page garde son propre bloc `:root { ... }` (les valeurs claires
     varient légèrement d'une page à l'autre) mais inclut ce partiel juste
     après pour ajouter les autres thèmes, sans dupliquer 9 fois les mêmes
     valeurs. Variables inutilisées par une page donnée : sans effet, pas
     d'erreur. Même caractère chaleureux que la palette claire (pas de
     noir/blanc pur), vérifié AA (≥4.5:1) texte sur fond et texte sur panneau. --}}
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

/* Halloween — mêmes valeurs et mêmes justifications de contraste que le
   bloc équivalent de resources/css/admin.css (texte principal 14.3:1,
   atténué 8.3:1, blanc sur --clay 4.65:1). */
:root[data-theme="halloween"] {
  --bg: #1A1013;
  --panel: #241722;
  --ink: #F0DFC9;
  --ink-soft: #C4A6A0;
  --clay: #B85A16;
  --line: #4A2E3D;
  --img-fallback: #2E1D26;
  --active-bg: #F0DFC9;
  --active-text: #1A1013;
  --danger: #E2544A;
  --field-bg: #2A1B23;
}

/* Noël — mêmes valeurs et mêmes justifications de contraste que le bloc
   équivalent de resources/css/admin.css (texte principal 15.2:1, atténué
   8.3:1, blanc sur --clay 8.2:1). */
:root[data-theme="noel"] {
  --bg: #0F1B14;
  --panel: #16261C;
  --ink: #F5EDE0;
  --ink-soft: #9FB8A8;
  --clay: #8C2F2F;
  --line: #2E4536;
  --img-fallback: #1C2E22;
  --active-bg: #F5EDE0;
  --active-text: #0F1B14;
  --danger: #E2665C;
  --field-bg: #1B2E22;
}

/* Les 4 blocs suivants reprennent les mêmes valeurs et les mêmes
   justifications de contraste que leurs équivalents dans
   resources/css/admin.css (voir les commentaires détaillés là-bas). */
:root[data-theme="midnight"] {
  --bg: #0B1220;
  --panel: #131C2E;
  --ink: #E7ECF5;
  --ink-soft: #9FB0C9;
  --clay: #2E5FA3;
  --line: #26324A;
  --img-fallback: #1A2438;
  --active-bg: #E7ECF5;
  --active-text: #0B1220;
  --danger: #E2796A;
  --field-bg: #182238;
}

:root[data-theme="sunset"] {
  --bg: #1D1220;
  --panel: #2A1B2E;
  --ink: #F5E5D8;
  --ink-soft: #CBA89E;
  --clay: #C1552E;
  --line: #4A2E45;
  --img-fallback: #2E1D2E;
  --active-bg: #F5E5D8;
  --active-text: #1D1220;
  --danger: #E2796A;
  --field-bg: #2A1A28;
}

:root[data-theme="safari"] {
  --bg: #1A1712;
  --panel: #24201A;
  --ink: #F0E8D8;
  --ink-soft: #B8AC8E;
  --clay: #7A6428;
  --line: #45402E;
  --img-fallback: #2E2A1F;
  --active-bg: #F0E8D8;
  --active-text: #1A1712;
  --danger: #E2796A;
  --field-bg: #221E17;
}

:root[data-theme="fullblack"] {
  --bg: #000000;
  --panel: #0D0D0D;
  --ink: #EDEDED;
  --ink-soft: #A0A0A0;
  --clay: #A8570F;
  --line: #262626;
  --img-fallback: #1A1A1A;
  --active-bg: #EDEDED;
  --active-text: #000000;
  --danger: #E2796A;
  --field-bg: #141414;
}
</style>
