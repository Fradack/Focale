<section>
    <h2>Informations du compte</h2>

    <form method="post" action="{{ route('admin.profile.update') }}">
        @csrf
        @method('patch')

        <div class="field">
            <label for="name">Nom</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            @error('name') <p style="color:var(--danger);font-size:12px;margin:4px 0 0;">{{ $message }}</p> @enderror
        </div>

        <div class="field">
            <label for="email">E-mail</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            @error('email') <p style="color:var(--danger);font-size:12px;margin:4px 0 0;">{{ $message }}</p> @enderror
        </div>

        <div style="display:flex;align-items:center;gap:14px;">
            <button type="submit" class="btn primary">Enregistrer</button>
            @if (session('status') === 'profile-updated')
                <span style="font-size:13px;color:var(--ok);">Enregistré.</span>
            @endif
        </div>
    </form>
</section>
