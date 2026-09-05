<x-install-layout :step="3" :title="'Connexion à la base de données'">
  <p class="subtitle">Renseigne les identifiants de la base MySQL/MariaDB fournie par ton hébergeur.</p>

  <form method="POST" action="{{ route('install.database.store') }}" id="db-form">
    @csrf
    <div class="field-row">
      <div class="field" style="flex:2;">
        <label>Hôte</label>
        <input type="text" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required>
        @error('db_host') <p class="error">{{ $message }}</p> @enderror
      </div>
      <div class="field">
        <label>Port</label>
        <input type="number" name="db_port" value="{{ old('db_port', 3306) }}" required>
      </div>
    </div>
    <div class="field">
      <label>Nom de la base</label>
      <input type="text" name="db_database" value="{{ old('db_database') }}" required>
      @error('db_database') <p class="error">{{ $message }}</p> @enderror
    </div>
    <div class="field-row">
      <div class="field">
        <label>Utilisateur</label>
        <input type="text" name="db_username" value="{{ old('db_username') }}" required>
      </div>
      <div class="field">
        <label>Mot de passe</label>
        <input type="password" name="db_password">
      </div>
    </div>

    <p id="test-result" class="notice" style="display:none;"></p>

    <button type="button" class="btn secondary" id="test-btn">Tester la connexion</button>
    <button type="submit" class="btn">Continuer</button>
  </form>

<script>
  document.getElementById('test-btn').addEventListener('click', async () => {
    const btn = document.getElementById('test-btn');
    const form = document.getElementById('db-form');
    const result = document.getElementById('test-result');
    const formData = new FormData(form);

    result.style.display = 'block';
    result.textContent = 'Test en cours…';
    result.style.color = 'var(--ink-soft)';
    btn.disabled = true;

    // Le serveur coupe déjà la connexion PDO au bout de 5s, mais on se
    // protège aussi côté navigateur pour ne jamais rester bloqué sur
    // "Test en cours…" (requête perdue, serveur qui ne répond plus, etc.).
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 8000);

    try {
      const response = await fetch(@json(route('install.database.test')), {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': formData.get('_token'), 'Accept': 'application/json' },
        body: formData,
        signal: controller.signal,
      });
      const data = await response.json();

      result.textContent = data.success ? 'Connexion réussie.' : (data.message || 'Connexion impossible.');
      result.style.color = data.success ? 'var(--ok)' : 'var(--danger)';
    } catch (err) {
      result.textContent = err.name === 'AbortError'
        ? "Le serveur ne répond pas (délai dépassé). Vérifie l'hôte et le port."
        : 'Erreur réseau pendant le test.';
      result.style.color = 'var(--danger)';
    } finally {
      clearTimeout(timeout);
      btn.disabled = false;
    }
  });
</script>
</x-install-layout>
