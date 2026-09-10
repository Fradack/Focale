@props(['notes'])

@php
  $blocks = \App\Support\ReleaseNotes::parse($notes);
@endphp

@if (empty($blocks))
  <p style="font-size:13px;color:var(--ink-soft);margin:0;">Aucune note de version fournie pour cette release.</p>
@else
  <div style="font-size:14px;line-height:1.7;color:var(--ink);">
    @foreach ($blocks as [$kind, $content])
      @if ($kind === 'list')
        <ul style="margin:0 0 12px;padding-left:20px;">
          @foreach ($content as $item)
            <li style="margin-bottom:4px;">
              @if ($item['badge'])
                <span class="badge {{ $item['badge']['class'] }}">{{ $item['badge']['label'] }}</span>
              @endif
              {{ $item['text'] }}
            </li>
          @endforeach
        </ul>
      @else
        <p style="margin:0 0 12px;">
          @if ($content['badge'])
            <span class="badge {{ $content['badge']['class'] }}">{{ $content['badge']['label'] }}</span>
          @endif
          {{ $content['text'] }}
        </p>
      @endif
    @endforeach
  </div>
@endif
