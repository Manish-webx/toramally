@extends('layouts.app')

@section('content')
<div class="wrap prose" style="max-width:820px;padding-bottom:96px">
  @include('partials.crumbs', ['items' => [['Home', ''], [$pg['title'], null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => $pg['title']])
  </div>
  {!! $pg['body_html'] !!}
  @if ($pg['slug'] === 'cookies')
    <button class="btn ghost" data-resetcookie>Change cookie choice</button>
  @endif
  <p class="small muted" style="margin-top:32px">Last updated {{ date('j F Y', strtotime($pg['updated_at'] ?? 'now')) }}.</p>
</div>
@endsection
