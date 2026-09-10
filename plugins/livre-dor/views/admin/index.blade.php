<x-admin-layout :active="'livre-dor'" :title="'Livre d\'or'">
  <div class="topbar">
    <div>
      <h1>Livre d'or <x-plugin-badge/></h1>
      <p>Messages laissés par les visiteurs sur les albums où le livre d'or est activé.</p>
    </div>
  </div>

  <div class="stats" style="margin-bottom:24px;">
    <div class="stat-card">
      <span class="value">{{ number_format($total) }}</span>
      <span class="label">Messages reçus</span>
    </div>
  </div>

  @forelse ($entries as $entry)
    <div class="panel" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <div>
          <strong>{{ $entry->author_name }}</strong>
          <span style="font-size:12px;color:var(--ink-soft);margin-left:8px;">
            sur <a href="{{ route('public.album', $entry->album) }}">{{ $entry->album->title }}</a>
          </span>
        </div>
        <span style="font-size:12px;color:var(--ink-soft);">{{ $entry->created_at->diffForHumans() }}</span>
      </div>

      <p style="margin:0 0 12px;font-size:14px;">{{ $entry->message }}</p>

      <div style="display:flex;justify-content:space-between;align-items:center;">
        <span class="badge" style="{{ $entry->status === 'approved' ? '' : 'opacity:.6;' }}">
          {{ $entry->status === 'approved' ? 'Approuvé' : 'En attente' }}
        </span>

        <div style="display:flex;gap:8px;">
          @if ($entry->status === 'pending')
            <form method="POST" action="{{ route('admin.livre-dor.approve', $entry) }}">
              @csrf
              @method('PATCH')
              <button type="submit" class="btn">Approuver</button>
            </form>
          @endif
          <form method="POST" action="{{ route('admin.livre-dor.destroy', $entry) }}" data-confirm="Supprimer ce message ?">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">Supprimer</button>
          </form>
        </div>
      </div>
    </div>
  @empty
    <div class="panel"><p style="margin:0;color:var(--ink-soft);">Aucun message pour l'instant.</p></div>
  @endforelse

  <x-pagination :paginator="$entries" />
</x-admin-layout>
