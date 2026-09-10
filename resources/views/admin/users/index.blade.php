<x-admin-layout :active="'users'" :title="'Utilisateurs'">
  @php
    $rolePillClass = [
      'admin' => 'published',
      'editor' => 'unlisted',
      'contributor' => 'draft',
      'viewer' => 'archived',
    ];
    $roleLabel = [
      'admin' => 'Admin',
      'editor' => 'Éditeur',
      'contributor' => 'Contributeur',
      'viewer' => 'Lecteur',
    ];
  @endphp

  <div class="topbar">
    <h1>Utilisateurs</h1>
    <a href="{{ route('admin.users.create') }}" class="new-btn">
      <svg viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"></path></svg>
      Créer
    </a>
  </div>

  @if (session('status') === 'user-created')
    <div class="alert alert-success">Utilisateur créé.</div>
  @elseif (session('status') === 'user-updated')
    <div class="alert alert-success">Utilisateur mis à jour.</div>
  @elseif (session('status') === 'user-deleted')
    <div class="alert alert-success">Utilisateur supprimé.</div>
  @elseif (session('status'))
    <div class="alert alert-danger">{{ session('status') }}</div>
  @endif

  @if ($users->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucun utilisateur.</p></div>
  @else
    <div class="table-panel">
      <table>
        <thead><tr><th>Nom</th><th>Email</th><th>Rôle</th><th>Créé le</th><th></th></tr></thead>
        <tbody>
          @foreach ($users as $user)
            <tr>
              <td>
                <div class="name">{{ $user->name }}{{ $user->id === auth()->id() ? ' (vous)' : '' }}</div>
              </td>
              <td>{{ $user->email }}</td>
              <td><span class="pill {{ $rolePillClass[$user->role] ?? 'draft' }}">{{ $roleLabel[$user->role] ?? $user->role }}</span></td>
              <td style="font-size:13px;color:var(--ink-soft);">{{ $user->created_at?->format('d/m/Y') }}</td>
              <td style="display:flex;gap:14px;align-items:center;">
                <a href="{{ route('admin.users.edit', $user) }}" style="font-size:13px;color:var(--ink-soft);">Modifier</a>
                @if ($user->id !== auth()->id())
                  <form method="POST" action="{{ route('admin.users.destroy', $user) }}" data-confirm="Supprimer définitivement cet utilisateur ?">
                    @csrf
                    @method('delete')
                    <button type="submit" style="background:none;border:none;padding:0;font-size:13px;color:var(--danger);cursor:pointer;font-family:inherit;">Supprimer</button>
                  </form>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  @endif
</x-admin-layout>
