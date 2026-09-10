<x-admin-layout :active="'faq'" :title="'FAQ'">
  <div class="topbar">
    <h1>FAQ</h1>
    <a href="{{ route('admin.faq.create') }}" class="new-btn">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
      Ajouter une question
    </a>
  </div>

  @if (session('status') === 'faq-created')
    <div class="alert alert-success">Question ajoutée.</div>
  @elseif (session('status') === 'faq-updated')
    <div class="alert alert-success">Question mise à jour.</div>
  @elseif (session('status') === 'faq-deleted')
    <div class="alert alert-success">Question supprimée.</div>
  @endif

  <p style="font-size:13px;color:var(--ink-soft);margin:-12px 0 20px;">
    Affichée sur <a href="{{ route('public.faq') }}" target="_blank" style="text-decoration:underline;">la page FAQ publique ↗</a>, groupée par catégorie.
  </p>

  @if ($items->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune question pour l'instant.</p></div>
  @else
    @foreach ($items as $category => $categoryItems)
      <div class="panel">
        <h2>{{ $category }}</h2>
        <div class="table-panel" style="border:none;">
          <table>
            <tbody>
              @foreach ($categoryItems as $item)
                <tr>
                  <td>{{ $item->question }}</td>
                  <td style="width:1%;white-space:nowrap;display:flex;gap:14px;align-items:center;">
                    <a href="{{ route('admin.faq.edit', $item) }}" style="font-size:13px;color:var(--ink-soft);">Modifier</a>
                    <form method="POST" action="{{ route('admin.faq.destroy', $item) }}" data-confirm="Supprimer définitivement cette question ?">
                      @csrf
                      @method('delete')
                      <button type="submit" style="background:none;border:none;padding:0;font-size:13px;color:var(--danger);cursor:pointer;font-family:inherit;">Supprimer</button>
                    </form>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    @endforeach
  @endif
</x-admin-layout>
