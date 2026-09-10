<x-admin-layout :active="'help'" :title="'Aide'">
  <div class="topbar">
    <h1>Aide</h1>
    <a href="{{ route('admin.faq.index') }}" class="btn">Gérer ces questions</a>
  </div>
  <p style="font-size:13px;color:var(--ink-soft);margin:-14px 0 24px;">FAQ réservée à l'administration — distincte de la FAQ publique consultée par les visiteurs du site.</p>

  <style>
    .help-tile { border: 1px solid var(--line); border-radius: 10px; background: var(--panel); margin-bottom: 10px; overflow: hidden; }
    .help-tile summary { list-style: none; cursor: pointer; padding: 14px 18px; font-size: 14px; font-weight: 500; display: flex; justify-content: space-between; align-items: center; gap: 12px; color: var(--ink); }
    .help-tile summary::-webkit-details-marker { display: none; }
    .help-tile summary::after { content: '+'; font-size: 18px; color: var(--ink-soft); flex-shrink: 0; transition: transform 0.15s ease; }
    .help-tile[open] summary::after { transform: rotate(45deg); }
    .help-tile .help-answer { padding: 0 18px 16px; font-size: 13px; line-height: 1.7; color: var(--ink-soft); }
    h2.help-section { font-family: 'Work Sans', sans-serif; font-weight: 500; font-size: 13px; text-transform: uppercase; letter-spacing: 0.04em; color: var(--ink-soft); margin: 28px 0 12px; }
    h2.help-section:first-of-type { margin-top: 0; }
  </style>

  @if ($items->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune question pour l'instant.</p></div>
  @else
    @foreach ($items as $category => $categoryItems)
      <h2 class="help-section">{{ $category }}</h2>
      @foreach ($categoryItems as $item)
        <details class="help-tile">
          <summary>{{ $item->question }}</summary>
          <div class="help-answer">{{ $item->answer }}</div>
        </details>
      @endforeach
    @endforeach
  @endif
</x-admin-layout>
