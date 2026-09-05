<section>
    <h2>Mot de passe</h2>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <x-password-field name="current_password" label="Mot de passe actuel" id="update_password_current_password"
            autocomplete="current-password" :bag="'updatePassword'" />

        <x-password-field name="password" label="Nouveau mot de passe" id="update_password_password"
            autocomplete="new-password" :bag="'updatePassword'"
            hint="9 caractères minimum, avec majuscule, minuscule, chiffre et caractère spécial." />

        <x-password-field name="password_confirmation" label="Confirmer le mot de passe" id="update_password_password_confirmation"
            autocomplete="new-password" :bag="'updatePassword'" />

        <div style="display:flex;align-items:center;gap:14px;">
            <button type="submit" class="btn primary">Enregistrer</button>
            @if (session('status') === 'password-updated')
                <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
            @endif
        </div>
    </form>
</section>
