<x-admin-layout :active="$active ?? null" :title="$title ?? 'Focale Admin'">
  <div class="topbar">
    <h1>{{ $title }}</h1>
  </div>
  <div class="panel">
    <p style="margin:0;color:var(--ink-soft);font-size:14px;">{{ $message ?? 'Cet écran arrive dans une prochaine étape.' }}</p>
  </div>
</x-admin-layout>
