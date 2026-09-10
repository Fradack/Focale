<x-admin-layout :active="'users'" :title="'Nouvel utilisateur'">
  <a href="{{ route('admin.users.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Utilisateurs</a>

  <div class="topbar">
    <h1>Nouvel utilisateur</h1>
  </div>

  <form method="POST" action="{{ route('admin.users.store') }}" style="max-width:520px;">
    @csrf
    <div class="panel">
      <div class="field">
        <label>Nom</label>
        <input type="text" name="name" value="{{ old('name') }}">
        @error('name') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}">
        @error('email') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="password">
        @error('password') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Rôle</label>
        <select name="role">
          <option value="admin" @selected(old('role') === 'admin')>Admin</option>
          <option value="editor" @selected(old('role') === 'editor')>Éditeur</option>
          <option value="contributor" @selected(old('role') === 'contributor')>Contributeur</option>
          <option value="viewer" @selected(old('role') === 'viewer')>Lecteur</option>
        </select>
        @error('role') <p class="error">{{ $message }}</p> @enderror
      </div>
    </div>
    <div class="panel" style="display:flex;gap:10px;">
      <button type="submit" class="btn primary" style="flex:1;">Créer</button>
    </div>
  </form>
</x-admin-layout>
