<x-mail::message>
# Nouveau message de contact

**De :** {{ $contactMessage->name }} ({{ $contactMessage->email }})
@if ($contactMessage->subject)
**Sujet :** {{ $contactMessage->subject }}
@endif

{{ $contactMessage->message }}

<x-mail::button :url="url('/administration/reglages')">
Voir dans l'administration
</x-mail::button>
</x-mail::message>
