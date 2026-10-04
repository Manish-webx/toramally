<div class="head">
  <div>
    <h1 class="h1">{{ $title }}</h1>
    @if (!empty($sub))
      <p style="margin-top:8px">{{ $sub }}</p>
    @endif
  </div>
  {!! $right ?? '' !!}
</div>
