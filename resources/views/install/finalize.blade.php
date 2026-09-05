<x-install-layout :step="4" :title="'Finaliser l\'installation'">
  <p class="subtitle">Tout est prêt. En cliquant ci-dessous, Focale va créer les tables de la base de données et ton compte administrateur.</p>

  <form method="POST" action="{{ route('install.finalize.store') }}">
    @csrf
    <button type="submit" class="btn">Installer Focale</button>
  </form>
</x-install-layout>
