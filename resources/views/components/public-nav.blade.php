@props(['active' => null])

<header>
  <a class="wordmark" href="{{ route('home') }}">{{ \App\Models\Setting::get('site_name', 'Focale') }}</a>
  <nav>
    <a href="{{ route('public.albums') }}">Albums</a>
    <a href="{{ route('public.gallery') }}">Galerie</a>
    @foreach (\App\Models\Page::inNav()->orderBy('title')->get() as $navPage)
      <a href="{{ route('page.show', $navPage) }}">{{ $navPage->title }}</a>
    @endforeach
    <a href="{{ route('public.contact') }}">Contact</a>
  </nav>
</header>
