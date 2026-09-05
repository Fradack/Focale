@props(['paginator'])
@if ($paginator->hasPages())
  <nav class="pagination-nav" aria-label="Pagination">
    @if ($paginator->onFirstPage())
      <span class="pagination-link disabled">‹ Précédent</span>
    @else
      <a href="{{ $paginator->previousPageUrl() }}" class="pagination-link">‹ Précédent</a>
    @endif

    <span class="pagination-pages">
      @foreach ($paginator->getUrlRange(1, $paginator->lastPage()) as $page => $url)
        @if ($page == $paginator->currentPage())
          <span class="pagination-link active">{{ $page }}</span>
        @else
          <a href="{{ $url }}" class="pagination-link">{{ $page }}</a>
        @endif
      @endforeach
    </span>

    @if ($paginator->hasMorePages())
      <a href="{{ $paginator->nextPageUrl() }}" class="pagination-link">Suivant ›</a>
    @else
      <span class="pagination-link disabled">Suivant ›</span>
    @endif
  </nav>
@endif
