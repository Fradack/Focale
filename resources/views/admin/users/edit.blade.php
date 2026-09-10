<x-admin-layout :active="'users'" :title="$user->name">
  <a href="{{ route('admin.users.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Utilisateurs</a>

  <div class="topbar">
    <h1>{{ $user->name }}</h1>
  </div>

  @if (session('status') === 'user-created')
    <div class="alert alert-success">Utilisateur créé.</div>
  @elseif (session('status') === 'user-updated')
    <div class="alert alert-success">Enregistré.</div>
  @elseif (session('status'))
    <div class="alert alert-danger">{{ session('status') }}</div>
  @endif

  <form method="POST" action="{{ route('admin.users.update', $user) }}" style="max-width:520px;">
    @csrf
    @method('put')
    <div class="panel">
      <div class="field">
        <label>Nom</label>
        <input type="text" name="name" value="{{ old('name', $user->name) }}">
        @error('name') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email', $user->email) }}">
        @error('email') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Nouveau mot de passe</label>
        <input type="password" name="password" placeholder="Laisser vide pour ne pas changer">
        @error('password') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Rôle</label>
        <select name="role">
          <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
          <option value="editor" @selected(old('role', $user->role) === 'editor')>Éditeur</option>
          <option value="contributor" @selected(old('role', $user->role) === 'contributor')>Contributeur</option>
          <option value="viewer" @selected(old('role', $user->role) === 'viewer')>Lecteur</option>
        </select>
        @error('role') <p class="error">{{ $message }}</p> @enderror
      </div>
    </div>
    <div class="panel" style="display:flex;gap:10px;">
      <button type="submit" class="btn primary" style="flex:1;">Enregistrer</button>
    </div>
  </form>

  @if ($user->id !== auth()->id())
    <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="max-width:520px;margin-top:20px;" data-confirm="Supprimer définitivement cet utilisateur ?">
      @csrf
      @method('delete')
      <button type="submit" class="btn" style="color:var(--danger);border-color:var(--danger);">Supprimer cet utilisateur</button>
    </form>
  @endif
</x-admin-layout>
