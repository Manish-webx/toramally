@php
    $c = craft_by_slug($slug);
    $i = craft_index($slug);
    $all = crafts();
@endphp
<div aria-label="Craft ladder: {{ $c['name'] ?? '' }}, rung {{ $i + 1 }} of {{ count($all) }}">
  <div class="lad-ind">
    @foreach ($all as $k => $l)
      {!! $k ? '<i></i>' : '' !!}<b class="{{ $k === $i ? 'on' : '' }}" title="{{ $l['name'] }}"></b>
    @endforeach
  </div>
  <div class="lad-cap">
    <a href="{{ url('craft/' . $slug) }}" style="text-decoration:none"><span class="serif-i" style="font-size:20px">{{ $c['name'] ?? '' }}</span></a>
    <span class="brass small">{{ $c['scale_word'] ?? '' }}</span>
  </div>
</div>
