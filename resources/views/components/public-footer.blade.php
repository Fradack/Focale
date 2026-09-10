@php
  $footerPages = \App\Models\Page::where('status', 'published')->orderBy('title')->get();
  $footerCopyright = \App\Models\Setting::get('footer_copyright')
      ?: '© '.now()->year.' '.(\App\Models\Setting::get('artist_name') ?: \App\Models\Setting::get('site_name', 'Focale')).'. Tous droits réservés.';
  $instagram = \App\Models\Setting::get('social_instagram');
  $twitter = \App\Models\Setting::get('social_twitter');
@endphp
<style>
  .site-footer { padding: 28px 6vw; border-top: 1px solid var(--line); font-size: 13px; color: var(--ink-soft); }
  .site-footer-links { display: flex; flex-wrap: wrap; gap: 8px 20px; margin-bottom: 14px; }
  .site-footer-links a { color: var(--ink-soft); text-decoration: underline; }
  .site-footer-links a:hover { color: var(--ink); }
  .site-footer-bottom { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 10px; }
</style>
<footer class="site-footer">
  <div class="site-footer-links">
    @foreach ($footerPages as $footerPage)
      <a href="{{ route('page.show', $footerPage) }}">{{ $footerPage->title }}</a>
    @endforeach
    @if ($instagram)
      <a href="{{ $instagram }}" target="_blank" rel="noopener">Instagram</a>
    @endif
    @if ($twitter)
      <a href="{{ $twitter }}" target="_blank" rel="noopener">Twitter / X</a>
    @endif
    @if (\App\Support\Plugins::enabled('tracking'))
      <a href="#" id="cookie-reopen-link">Cookies</a>
    @endif
    @if (\App\Support\Plugins::enabled('avis') && \Illuminate\Support\Facades\Route::has('public.avis.create'))
      <a href="{{ route('public.avis.create') }}">Donner mon avis</a>
    @endif
    <a href="{{ route('login') }}">Administration</a>
  </div>
  <div class="site-footer-bottom">
    <span>{{ $footerCopyright }}</span>
    <span>Focale v{{ config('focale.version') }}</span>
  </div>
</footer>

<x-cookie-consent-banner :consent="request()->cookie('focale_consent')"/>
@includeIf('plugins.tracking.beacon')
<x-antipillage-guard/>
