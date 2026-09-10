<x-admin-layout :active="'avis'" :title="'Avis'">
  <div class="topbar">
    <div>
      <h1>Avis <x-plugin-badge/></h1>
      <p>Avis laissés par les visiteurs et clients sur leur expérience du site.</p>
    </div>
    <a href="{{ route('admin.avis.questions.index') }}" class="btn">Gérer les questions</a>
  </div>

  <div class="stats" style="margin-bottom:24px;">
    <div class="stat-card">
      <span class="value">{{ number_format($total) }}</span>
      <span class="label">Avis reçus</span>
    </div>
  </div>

  @forelse ($reviews as $review)
    <div class="panel" style="margin-bottom:16px;">
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;">
        <strong>{{ $review->user?->name ?? 'Anonyme' }}</strong>
        <span style="font-size:12px;color:var(--ink-soft);">{{ $review->submitted_at?->diffForHumans() }}</span>
      </div>
      @if ($review->answers->isEmpty())
        <p style="font-size:13px;color:var(--ink-soft);margin:0;">Aucune réponse renseignée.</p>
      @else
        <dl style="margin:0;">
          @foreach ($review->answers as $answer)
            <div style="padding:8px 0;border-top:1px solid var(--line);">
              <dt style="font-size:12px;color:var(--ink-soft);margin-bottom:4px;">{{ $answer->question_text }}</dt>
              <dd style="margin:0;font-size:14px;">
                @if ($answer->answer_rating !== null)
                  {{ str_repeat('★', $answer->answer_rating) }}{{ str_repeat('☆', 5 - $answer->answer_rating) }}
                @else
                  {{ $answer->answer_text }}
                @endif
              </dd>
            </div>
          @endforeach
        </dl>
      @endif
    </div>
  @empty
    <div class="panel"><p style="margin:0;color:var(--ink-soft);">Aucun avis reçu pour l'instant.</p></div>
  @endforelse

  <x-pagination :paginator="$reviews" />
</x-admin-layout>
