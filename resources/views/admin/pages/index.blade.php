<x-admin-layout :active="'pages'" :title="'Pages'">
  <div class="topbar">
    <h1>Pages</h1>
    <form method="POST" action="{{ route('admin.pages.store') }}" style="display:flex;gap:8px;">
      @csrf
      <input type="text" name="title" placeholder="Titre de la nouvelle page" required style="padding:10px 14px;border:1px solid var(--line);border-radius:8px;background:var(--panel);font-size:14px;">
      <button type="submit" class="new-btn">
        <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
        Créer
      </button>
    </form>
  </div>

  <p style="font-size:13px;color:var(--ink-soft);margin:-14px 0 20px;">
    Pages de contenu simples (hors portfolios) — mentions légales, CGU, À propos… L'éditeur par blocs complet est prévu après le MVP,
    pour l'instant un seul bloc de texte par page.
  </p>

  @if ($pages->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune page pour l'instant.</p></div>
  @else
    <div class="table-panel">
      <table>
        <thead><tr><th>Page</th><th>Statut</th><th></th></tr></thead>
        <tbody>
          @foreach ($pages as $page)
            <tr>
              <td>
                <div class="name">{{ $page->title }}</div>
                <div class="slug">/{{ $page->slug }}</div>
              </td>
              <td><span class="pill {{ $page->status === 'published' ? 'published' : 'draft' }}">{{ $page->status === 'published' ? 'Publiée' : 'Brouillon' }}</span></td>
              <td><a href="{{ route('admin.pages.edit', $page) }}" style="font-size:13px;color:var(--ink-soft);">Modifier</a></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</x-admin-layout>
