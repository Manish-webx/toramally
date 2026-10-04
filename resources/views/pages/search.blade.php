@extends('layouts.app')

@section('content')
<div class="wrap" style="padding-bottom:96px">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Search', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Search'])
  </div>
  <form action="{{ url('search') }}" method="get" role="search" class="row" style="margin-bottom:32px">
    <label class="vh" for="sq">Search</label>
    <input id="sq" name="q" value="{{ $q }}" class="field" style="flex:1;min-height:48px;border:1px solid var(--line);background:var(--ivory-50);padding:0 12px;margin:0">
    <button class="btn" type="submit">Search</button>
  </form>
  @php $any = array_filter($results); @endphp
  @if ($q !== '' && !$any)
    <p>Nothing found for “{{ $q }}”. Try Belgian, Miniature or Wedding.</p>
    <p class="serif-i" style="font-size:22px">Not quite what you imagined? <a href="{{ url('bespoke/build') }}">Commission it.</a></p>
  @endif
  <div class="sres">
    @foreach ($results as $g => $list)
      @if (!empty($list))
        <h4>{{ $g }}</h4>
        @foreach ($list as $r)
          <a href="{{ $r['h'] }}">{{ $r['t'] }}</a>
        @endforeach
      @endif
    @endforeach
  </div>
</div>
@endsection
