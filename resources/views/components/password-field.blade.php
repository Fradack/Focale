@props(['name', 'label', 'id' => null, 'required' => true, 'hint' => null, 'bag' => 'default'])
@php($id = $id ?? $name)
<div class="field">
  <label for="{{ $id }}">{{ $label }}</label>
  <div class="password-wrap">
    <input type="password" id="{{ $id }}" name="{{ $name }}" @if ($required) required @endif {{ $attributes }}>
    <button type="button" class="password-toggle" data-for="{{ $id }}" aria-label="Afficher le mot de passe">
      <svg class="icon-on" viewBox="0 0 24 24"><path d="M1 12s4-7 11-7 11 7 11 7-4 7-11 7-11-7-11-7z"></path><circle cx="12" cy="12" r="3"></circle></svg>
      <svg class="icon-off" viewBox="0 0 24 24"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-10-8-10-8a18.5 18.5 0 015.06-5.94M9.9 4.24A10.6 10.6 0 0112 4c7 0 10 8 10 8a18.6 18.6 0 01-2.16 3.19M14.12 14.12a3 3 0 11-4.24-4.24"></path><path d="M1 1l22 22"></path></svg>
    </button>
  </div>
  @if ($hint)
    <p class="field-hint">{{ $hint }}</p>
  @endif
  @error($name, $bag)
    <p class="error">{{ $message }}</p>
  @enderror
</div>
