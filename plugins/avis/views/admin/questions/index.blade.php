<x-admin-layout :active="'avis'" :title="'Questions — Avis'">
  <a href="{{ route('admin.avis.index') }}" style="display:inline-flex;font-size:13px;color:var(--ink-soft);margin-bottom:18px;">← Avis</a>

  <div class="topbar">
    <h1>Questions du questionnaire <x-plugin-badge/></h1>
  </div>

  @if (session('status'))
    <div class="alert alert-success">Enregistré.</div>
  @endif

  @if ($questions->isNotEmpty())
    <div class="table-panel" style="margin-bottom:24px;overflow-x:auto;">
      <table>
        <thead>
          <tr>
            <th>Question</th>
            <th style="width:170px;">Type</th>
            <th style="width:80px;">Ordre</th>
            <th style="width:80px;">Active</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach ($questions as $question)
            <tr>
              <td><input form="rq-update-{{ $question->id }}" type="text" name="question" value="{{ $question->question }}" style="width:100%;"></td>
              <td>
                <select form="rq-update-{{ $question->id }}" name="type" style="width:100%;">
                  @foreach (\App\Plugins\Avis\Models\ReviewQuestion::TYPES as $value => $label)
                    <option value="{{ $value }}" @selected($question->type === $value)>{{ $label }}</option>
                  @endforeach
                </select>
              </td>
              <td><input form="rq-update-{{ $question->id }}" type="number" name="sort_order" value="{{ $question->sort_order }}" style="width:100%;"></td>
              <td><input form="rq-update-{{ $question->id }}" type="checkbox" name="active" value="1" @checked($question->active)></td>
              <td style="display:flex;gap:8px;">
                <button form="rq-update-{{ $question->id }}" type="submit" class="btn" style="padding:6px 12px;font-size:12px;">Enregistrer</button>
                <button form="rq-delete-{{ $question->id }}" type="submit" style="padding:6px 12px;font-size:12px;color:var(--danger);">Supprimer</button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    @foreach ($questions as $question)
      <form id="rq-update-{{ $question->id }}" method="POST" action="{{ route('admin.avis.questions.update', $question) }}">
        @csrf
        @method('PUT')
      </form>
      <form id="rq-delete-{{ $question->id }}" method="POST" action="{{ route('admin.avis.questions.destroy', $question) }}" data-confirm="Supprimer la question « {{ $question->question }} » ?">
        @csrf
        @method('DELETE')
      </form>
    @endforeach
  @else
    <div class="panel" style="margin-bottom:24px;"><p style="margin:0;color:var(--ink-soft);">Aucune question pour l'instant — le formulaire public restera vide tant qu'aucune n'est ajoutée.</p></div>
  @endif

  <div class="panel" style="max-width:600px;">
    <h2>Ajouter une question</h2>
    <form method="POST" action="{{ route('admin.avis.questions.store') }}">
      @csrf
      <div class="field">
        <label>Question</label>
        <input type="text" name="question" value="{{ old('question') }}" placeholder="Ex. Comment s'est passée votre expérience sur le site ?">
        @error('question') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Type de réponse</label>
        <select name="type">
          @foreach (\App\Plugins\Avis\Models\ReviewQuestion::TYPES as $value => $label)
            <option value="{{ $value }}" @selected(old('type') === $value)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="field">
        <label>Ordre d'affichage</label>
        <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}">
      </div>
      <button type="submit" class="btn primary">Ajouter</button>
    </form>
  </div>
</x-admin-layout>
