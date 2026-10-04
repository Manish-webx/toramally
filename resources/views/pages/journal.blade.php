@extends('layouts.app')

@section('content')
@php
    $f = $posts[0] ?? null;
    $rest = array_slice($posts, 1);
    $cslug = fn($a) => craft_by_id((int) ($a['craft_id'] ?? 0))['slug'] ?? 'patina';
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Journal', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Journal', 'sub' => 'Notes on craft, style, materials, people and places.'])
  </div>
  @if ($f)
    <a class="split" href="{{ url('journal/' . $f['slug']) }}" style="text-decoration:none">
      <div class="art wide">
        @if (!empty($f['cover_path']))
          <img src="{{ asset($f['cover_path']) }}" alt="" loading="lazy" style="width:100%;height:100%;object-fit:cover">
        @else
          {!! macro_slot($cslug($f), craft_colour($cslug($f))) !!}
        @endif
      </div>
      <div>
        <p class="label brass">{{ $f['category'] }}</p>
        <h2 class="h1" style="margin:8px 0 12px">{{ $f['title'] }}</h2>
        <p>{{ $f['dek'] }}</p>
        <span class="tlink">Read</span>
      </div>
    </a>
  @endif
  <div class="grid g3 section">
    @foreach ($rest as $a)
      <a class="tile" href="{{ url('journal/' . $a['slug']) }}">
        <div class="img" style="aspect-ratio:3/2">
          @if (!empty($a['cover_path']))
            <img src="{{ asset($a['cover_path']) }}" alt="" loading="lazy">
          @else
            {!! macro_slot($cslug($a), craft_colour($cslug($a))) !!}
          @endif
        </div>
        <p class="label brass" style="margin-top:14px">{{ $a['category'] }}</p>
        <h3 class="h3" style="margin-top:4px">{{ $a['title'] }}</h3>
        <p class="small muted">{{ $a['dek'] }}</p>
      </a>
    @endforeach
  </div>
  @if (!empty($soon))
    <div style="padding-bottom:96px">
      <div class="head"><h2 class="h3">In preparation</h2></div>
      <p class="small muted">{{ implode('. ', array_column($soon, 'title')) }}.</p>
    </div>
  @endif
</div>
@endsection
