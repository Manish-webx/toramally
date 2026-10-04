@extends('layouts.app')

@section('content')
<div class="wrap" style="padding-bottom:96px">
  @include('partials.crumbs', ['items' => [['Home', ''], ['The House', 'house'], ['Worn by', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Worn by', 'sub' => 'Clients and friends of the house, in their Tōramally pairs. Shared with their permission.'])
  </div>
  @if (!empty($people))
    <div class="grid g3">
      @foreach ($people as $c)
        <figure class="tile" style="margin:0">
          <div class="img">{!! !empty($c['photo_path']) ? '<img src="' . e(asset($c['photo_path'])) . '" alt="' . e($c['name'] . ' wearing Tōramally') . '" loading="lazy">' : macro_slot('patina', 'Oxblood') !!}</div>
          <figcaption>
            <h3 class="h3">{{ $c['name'] }}</h3>
            <p class="small muted">{{ implode('. ', array_filter([$c['occasion'] ?? '', $c['pair_worn'] ?? ''])) }}</p>
          </figcaption>
        </figure>
      @endforeach
    </div>
  @else
    <p class="serif-i" style="font-size:24px">The first portraits are being prepared.</p>
    <p>In the meantime, see the house on <a href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram</a>.</p>
  @endif
</div>
@endsection
