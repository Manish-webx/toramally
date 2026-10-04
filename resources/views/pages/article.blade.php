@extends('layouts.app')

@section('content')
<article class="wrap prose" style="max-width:820px">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Journal', 'journal'], [$a['title'], null]]])
  <p class="label brass" style="margin-top:32px">{{ $a['category'] }}</p>
  <h1 class="h1" style="margin:8px 0 12px">{{ $a['title'] }}</h1>
  <p class="serif-i" style="font-size:24px;color:#5d5a52">{{ $a['dek'] }}</p>
  <div class="art wide" style="margin:32px 0">
    @if (!empty($a['cover_path']))
      <img src="{{ asset($a['cover_path']) }}" alt="" style="width:100%;height:100%;object-fit:cover">
    @else
      {!! macro_slot($craft['slug'] ?? 'patina', craft_colour($craft['slug'] ?? 'patina')) !!}
    @endif
  </div>
  {!! $a['body_html'] !!}
  <div class="row" style="margin:32px 0">
    @if ($craft)
      <a class="tlink" href="{{ url('craft/' . $craft['slug']) }}">About {{ $craft['name'] }}</a>
    @endif
    <button class="tlink" data-sharepage>Share</button>
  </div>
</article>

@if (!empty($related))
  <section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">Pairs from this story</h2></div>
    <div class="grid g3">
      @foreach ($related as $p)
        @include('partials.product-card', ['p' => $p])
      @endforeach
    </div>
  </div></section>
@endif
@endsection
