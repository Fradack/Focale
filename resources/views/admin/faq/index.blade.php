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

  <h2 style="font-family:'Fraunces',serif;font-weight:500;font-size:18px;margin:0 0 4px;">FAQ Visiteurs</h2>
  <p style="font-size:13px;color:var(--ink-soft);margin:0 0 16px;">Affichée sur <a href="{{ route('public.faq') }}" target="_blank" style="text-decoration:underline;">la page FAQ publique ↗</a>.</p>
  @if ($visitorItems->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune question pour l'instant.</p></div>
  @else
    @include('admin.faq._groups', ['groups' => $visitorItems])
  @endif

  <h2 style="font-family:'Fraunces',serif;font-weight:500;font-size:18px;margin:32px 0 4px;">FAQ Administrateur</h2>
  <p style="font-size:13px;color:var(--ink-soft);margin:0 0 16px;">Réservée à l'équipe, consultable en lecture dans <a href="{{ route('admin.help.index') }}" style="text-decoration:underline;">Aide</a> — jamais visible des visiteurs.</p>
  @if ($adminItems->isEmpty())
    <div class="panel"><p style="margin:0;color:var(--ink-soft);font-size:14px;">Aucune question pour l'instant.</p></div>
  @else
    @include('admin.faq._groups', ['groups' => $adminItems])
  @endif
</x-admin-layout>
