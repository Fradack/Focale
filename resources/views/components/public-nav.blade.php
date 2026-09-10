@props(['active' => null])

<header>
  <a class="wordmark" href="{{ route('home') }}">{{ \App\Models\Setting::get('site_name', 'Focale') }}</a>
  <nav>
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
