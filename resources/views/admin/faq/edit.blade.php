<x-admin-layout :active="'faq'" :title="'Modifier la question'">
  <a href="{{ route('admin.faq.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← FAQ</a>

  <div class="topbar">
    <h1>Modifier la question</h1>
  </div>

  <form method="POST" action="{{ route('admin.faq.update', $item) }}" style="max-width:600px;">
    @csrf
    @method('put')
    <div class="panel">
      <div class="field">
        <label>Public visé</label>
        <select name="audience">
          <option value="visitor" @selected(old('audience', $item->audience) === 'visitor')>Visiteurs (FAQ publique)</option>
          <option value="admin" @selected(old('audience', $item->audience) === 'admin')>Administrateur (aide interne, jamais publique)</option>
        </select>
        @error('audience') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Catégorie</label>
        <input type="text" name="category" value="{{ old('category', $item->category) }}">
        @error('category') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Question</label>
        <input type="text" name="question" value="{{ old('question', $item->question) }}">
        @error('question') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Réponse</label>
        <textarea name="answer" rows="5">{{ old('answer', $item->answer) }}</textarea>
        @error('answer') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field" style="max-width:160px;">
        <label>Ordre</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', $item->sort_order) }}">
      </div>
    </div>
    <div class="panel" style="display:flex;gap:10px;">
      <button type="submit" class="btn primary" style="flex:1;">Enregistrer</button>
    </div>
  </form>

  <form method="POST" action="{{ route('admin.faq.destroy', $item) }}" data-confirm="Supprimer définitivement cette question ?" style="max-width:600px;margin-top:10px;">
    @csrf
    @method('delete')
    <button type="submit" class="btn" style="color:var(--danger);">Supprimer</button>
  </form>
</x-admin-layout>
