@props(['active' => null])

<style>
  @media (max-width: 720px) {
    header { flex-wrap: wrap; row-gap: 10px; }
    header nav {
      display: none;
      width: 100%;
      flex-direction: column;
      gap: 14px;
      padding-top: 14px;
      border-top: 1px solid var(--line, #C9C2B4);
    }
    header nav.is-open { display: flex; }
    #public-nav-toggle { display: inline-flex; }
  }
  #public-nav-toggle {
    display: none;
    align-items: center;
    justify-content: center;
    width: 36px;
    height: 36px;
    border: 1px solid var(--line, #C9C2B4);
    border-radius: 8px;
    background: none;
    cursor: pointer;
    color: inherit;
  }
  #public-nav-toggle svg { width: 18px; height: 18px; stroke: currentColor; fill: none; stroke-width: 1.6; }
</style>

<header>
  <a class="wordmark" href="{{ route('home') }}">{{ \App\Models\Setting::get('site_name', 'Focale') }}</a>
  <button type="button" id="public-nav-toggle" aria-label="Ouvrir le menu" aria-expanded="false">
    <svg viewBox="0 0 24 24"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
  </button>
  <nav id="public-nav">
    <a href="{{ route('public.albums') }}">Albums</a>
    <a href="{{ route('public.gallery') }}">Galerie</a>
    @if (\App\Models\Setting::get('shop_enabled'))
      <a href="{{ route('public.shop.index') }}">Boutique</a>
    @endif
    @foreach (\App\Models\Page::inNav()->orderBy('title')->get() as $navPage)
      <a href="{{ route('page.show', $navPage) }}">{{ $navPage->title }}</a>
    @endforeach
    <a href="{{ route('public.faq') }}">FAQ</a>
    <a href="{{ route('public.contact') }}">Contact</a>
    @if (\App\Models\Setting::get('shop_enabled'))
      @php($cartCount = app(\App\Services\Cart::class)->count())
      <a href="{{ route('public.cart.show') }}">Panier @if ($cartCount > 0) ({{ $cartCount }}) @endif</a>
    @endif
    @if (auth()->check() && auth()->user()->is_customer)
      <a href="{{ route('customer.dashboard') }}">{{ auth()->user()->name }}</a>
    @else
      <a href="{{ route('customer.login') }}">Connexion</a>
    @endif
  </nav>
</header>

<script>
  (function () {
    var toggle = document.getElementById('public-nav-toggle');
    var nav = document.getElementById('public-nav');
    if (!toggle || !nav) return;
    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  })();
</script>
