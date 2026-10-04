@php
    $c1 = $p['colours'][0]['name'] ?? 'Black';
    $c2 = ($p['colours'][1] ?? $p['colours'][0])['name'] ?? $c1;
@endphp
<a class="card" href="{{ product_url($p) }}">
  <div class="img">
    <div class="main" style="width:100%;height:100%;display:grid;place-items:center">{!! product_visual($p, $c1) !!}</div>
    <div class="alt">{!! product_visual($p, $c2) !!}</div>
    <span class="tag">{{ ($p['status'] ?? '') === 'Sold' ? 'Sold' : availability_label($p) }}</span>
  </div>
  <h3>{{ $p['name'] }}</h3>
  <div class="meta">{{ $p['silhouette'] . ', ' . ($p['craft_name'] ?? '') }}</div>
  <div class="row2"><span class="price">{!! money((int) $p['base_price']) !!}</span>
    <span class="dots" aria-label="Colours: {{ implode(', ', array_column($p['colours'] ?? [], 'name')) }}">
      @foreach ($p['colours'] ?? [] as $c)
        <span style="background:{{ $c['hex'] }}" title="{{ $c['name'] }}"></span>
      @endforeach
    </span>
  </div>
</a>
