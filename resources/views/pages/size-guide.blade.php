@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Size guide', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Size guide', 'sub' => 'Measure once, carefully, and keep the number.'])
  </div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <h2 class="h3">Measure your foot</h2>
      <ol class="steps4" style="grid-template-columns:1fr;margin-top:16px">
        <li><p>Stand on a sheet of paper against a wall, heel touching the wall.</p></li>
        <li><p>Mark the tip of your longest toe.</p></li>
        <li><p>Measure from the wall to the mark in centimetres. Do both feet and use the longer.</p></li>
        <li><p>Compare with the table, or send us the number.</p></li>
      </ol>
      @include('partials.wa-button', ['label' => 'Help me find my size', 'text' => 'Hello Tōramally, my foot measures ___ cm. Which size should I choose?'])
    </div>
    <div class="scrollx">
      <table class="sz">
        <thead>
          <tr><th>Foot (cm)</th><th>India</th><th>UK</th><th>EU</th><th>US men</th></tr>
        </thead>
        <tbody>
          @for ($n = 5; $n <= 12; $n += 0.5)
            <tr><td>{{ number_format(23.5 + ($n - 5) * 0.85, 1) }}</td><td>{{ $n }}</td><td>{{ $n }}</td><td>{{ $n + 34 }}</td><td>{{ $n + 1 }}</td></tr>
          @endfor
        </tbody>
      </table>
      <p class="small muted" style="margin-top:12px">Women: EU is UK + 33, US is UK + 2. Fit varies by last; we will advise for your pair.</p>
    </div>
  </div>
</div>
@endsection
