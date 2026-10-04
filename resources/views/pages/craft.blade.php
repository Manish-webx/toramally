@extends('layouts.app')

@section('content')
@php
    $all = crafts();
    $i = craft_index($c['slug']);
    $prev = $all[$i - 1] ?? null;
    $next = $all[$i + 1] ?? null;
    $title = $c['slug'] === 'scarring' ? 'Scarring (laser)' : $c['name'];
    $buildCraft = in_array($c['slug'], ['velvet', 'bespoke'], true) ? '' : '?craft=' . $c['slug'];
@endphp

<section class="dark"><div class="wrap split" style="padding-top:40px;padding-bottom:64px">
  <div>
    @include('partials.crumbs', ['items' => [['Home', ''], ['Craft', 'craft'], [$c['name'], null]], 'light' => true])
    <h1 class="h1" style="margin-top:24px">{{ $title }}</h1>
    <p class="serif-i" style="font-size:26px;color:#cdb47f;margin:8px 0 20px">{{ $c['tagline'] }}</p>
    <p class="muted">{{ $c['description'] }}</p>
    <p class="small muted">{!! $c['from_price'] ? 'From ' . money((int) $c['from_price']) . '.' : 'Priced after consultation.' !!} Lead time {{ $c['lead_weeks'] }} weeks. {{ $c['buy_mode_note'] }}.</p>
    <div class="row" style="margin-top:12px">
      <a class="btn" href="{{ url('bespoke/build' . $buildCraft) }}">{{ $c['slug'] === 'velvet' ? 'Build a pair' : 'Customise in ' . $c['name'] }}</a>
      @if (!empty($pairs))
        <a class="btn ghost" href="{{ url('shop?craft=' . $c['slug']) }}">Shop {{ $c['name'] }}</a>
      @endif
    </div>
  </div>
  <div class="art" style="aspect-ratio:1">{!! macro_slot($c['slug'], craft_colour($c['slug'])) !!}</div>
</div></section>

<section class="section"><div class="wrap">
  <div class="head">
    <h2 class="h2">How it is made</h2>
    @include('partials.ladder-indicator', ['slug' => $c['slug']])
  </div>
  <ol class="steps4" style="grid-template-columns:repeat(auto-fit,minmax(220px,1fr))">
    @foreach ([['The material', $c['how_1']], ['The hand', $c['how_2']], ['The result', $c['how_3']]] as [$h, $t])
      <li><h3 class="h3">{{ $h }}</h3><p class="small" style="margin-top:6px">{{ $t }}</p></li>
    @endforeach
  </ol>
  @if ($c['slug'] === 'scarring')
    <div class="grid g2" style="margin-top:48px">
      <figure style="margin:0"><div class="art" style="aspect-ratio:1">{!! macro_slot('patina', 'Dark Brown', 'Patinated calf before scarring') !!}</div><figcaption class="small muted" style="margin-top:8px">Before: patinated calf.</figcaption></figure>
      <figure style="margin:0"><div class="art" style="aspect-ratio:1">{!! macro_slot('scarring', 'Dark Brown', 'The same calf after scarring') !!}</div><figcaption class="small muted" style="margin-top:8px">After: the lattice, lifted by light.</figcaption></figure>
    </div>
  @endif
</div></section>

@if (!empty($pairs))
  <section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">Pairs in {{ $c['name'] }}</h2><a class="tlink" href="{{ url('shop?craft=' . $c['slug']) }}">All</a></div>
    <div class="grid g3">
      @foreach ($pairs as $p)
        @include('partials.product-card', ['p' => $p])
      @endforeach
    </div></div></section>
@endif

<section class="section" style="padding-top:48px"><div class="wrap row" style="justify-content:space-between;border-top:1px solid var(--line);padding-top:24px">
  @if ($prev)
    <a class="tlink" href="{{ url('craft/' . $prev['slug']) }}">Step back: {{ $prev['name'] }}</a>
  @else
    <span></span>
  @endif
  <a class="tlink" href="{{ url('care') }}">{{ $c['name'] }} care</a>
  @if ($next)
    <a class="tlink" href="{{ url('craft/' . $next['slug']) }}">Step up: {{ $next['name'] }}</a>
  @else
    <span></span>
  @endif
</div></section>
@endsection
