@extends('layouts.app')

@section('content')
@php
    $embed = function (string $u): ?string {
        if (preg_match('~(?:youtu\.be/|v=|embed/)([\w-]{11})~', $u, $m)) return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        if (preg_match('~vimeo\.com/(\d+)~', $u, $m)) return 'https://player.vimeo.com/video/' . $m[1];
        return null;
    };
@endphp

<div class="wrap" style="padding-bottom:96px">
  @include('partials.crumbs', ['items' => [['Home', ''], ['The House', 'house'], ['Videos', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Videos', 'sub' => 'Films of the making: brush, beam, needle and blade.'])
  </div>
  @if (!empty($videos))
    <div class="grid g2">
      @foreach ($videos as $v)
        @php $src = $embed($v['url']); @endphp
        <figure style="margin:0">
          <div class="art wide">
            @if ($src)
              <iframe src="{{ $src }}" title="{{ $v['title'] }}" loading="lazy" allow="encrypted-media; picture-in-picture" allowfullscreen style="width:100%;height:100%;border:0"></iframe>
            @else
              <video src="{{ asset($v['url']) }}" controls preload="none" style="width:100%;height:100%"{!! !empty($v['poster_path']) ? ' poster="' . e(asset($v['poster_path'])) . '"' : '' !!}></video>
            @endif
          </div>
          <figcaption>
            <h3 class="h3" style="margin-top:12px">{{ $v['title'] }}</h3>
            @if (!empty($v['caption']))
              <p class="small muted">{{ $v['caption'] }}</p>
            @endif
          </figcaption>
        </figure>
      @endforeach
    </div>
  @else
    <p class="serif-i" style="font-size:24px">Films from the workshop are on their way.</p>
    <p>Until then, watch the making on <a href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram</a>.</p>
  @endif
</div>
@endsection
