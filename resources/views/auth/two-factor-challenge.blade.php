<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Saisis le code à 6 chiffres de ton application d\'authentification, ou l\'un de tes codes de récupération.') }}
    </div>

    <form method="POST" action="{{ route('two-factor.challenge.store') }}">
        @csrf

        <div>
            <x-input-label for="code" :value="__('Code')" />
            <x-text-input id="code" class="block mt-1 w-full" type="text" name="code" inputmode="numeric" required autofocus autocomplete="one-time-code" />
            <x-input-error :messages="$errors->get('code')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button>
                {{ __('Vérifier') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
