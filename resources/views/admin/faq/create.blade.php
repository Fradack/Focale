<x-admin-layout :active="'faq'" :title="'Nouvelle question'">
  <a href="{{ route('admin.faq.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← FAQ</a>

  <div class="topbar">
    <h1>Nouvelle question</h1>
  </div>

  <form method="POST" action="{{ route('admin.faq.store') }}" style="max-width:600px;">
    @csrf
    <div class="panel">
      <div class="field">
        <label>Public visé</label>
        <select name="audience">
          <option value="visitor" @selected(old('audience', 'visitor') === 'visitor')>Visiteurs (FAQ publique)</option>
          <option value="admin" @selected(old('audience') === 'admin')>Administrateur (aide interne, jamais publique)</option>
        </select>
        @error('audience') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Catégorie</label>
        <input type="text" name="category" value="{{ old('category') }}" placeholder="Ex. Parcourir le site" list="faq-categories">
        <datalist id="faq-categories">
          @foreach ($categories as $category)
            <option value="{{ $category }}">
          @endforeach
        </datalist>
        @error('category') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Question</label>
        <input type="text" name="question" value="{{ old('question') }}">
        @error('question') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Réponse</label>
        <textarea name="answer" rows="5">{{ old('answer') }}</textarea>
        @error('answer') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field" style="max-width:160px;">
        <label>Ordre</label>
        <input type="number" name="sort_order" value="{{ old('sort_order') }}" placeholder="Auto">
        <p class="field-hint">Laisser vide pour l'ajouter à la fin de sa catégorie.</p>
      </div>
    </div>
    <div class="panel" style="display:flex;gap:10px;">
      <button type="submit" class="btn primary" style="flex:1;">Créer</button>
    </div>
  </form>
</x-admin-layout>
