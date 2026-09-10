@foreach ($groups as $category => $categoryItems)
  <div class="panel">
    <h2>{{ $category }}</h2>
    <div class="table-panel" style="border:none;">
      <table>
        <tbody>
          @foreach ($categoryItems as $item)
            <tr>
              <td>{{ $item->question }}</td>
              <td style="width:1%;white-space:nowrap;display:flex;gap:14px;align-items:center;">
                <a href="{{ route('admin.faq.edit', $item) }}" style="font-size:13px;color:var(--ink-soft);">Modifier</a>
                <form method="POST" action="{{ route('admin.faq.destroy', $item) }}" data-confirm="Supprimer définitivement cette question ?">
                  @csrf
                  @method('delete')
                  <button type="submit" style="background:none;border:none;padding:0;font-size:13px;color:var(--danger);cursor:pointer;font-family:inherit;">Supprimer</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
@endforeach
