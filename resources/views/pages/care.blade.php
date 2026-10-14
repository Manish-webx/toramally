@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Care Guide', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'Care Guide', 
      'sub' => 'A Tōramally is made to be worn for years and handed on. A little care keeps it that way.'
    ])
  </div>

  <div class="grid g3" style="gap:24px;margin-top:24px;padding-bottom:48px">
    <!-- Every Day -->
    <div class="tile" style="padding:28px">
      <h2 class="h3">Every day</h2>
      <ul style="margin:16px 0 0 18px;line-height:1.7">
        <li>Use cedar shoe trees when the shoes are off — they hold the shape and draw out moisture.</li>
        <li>Rest a pair for a day between wears.</li>
        <li>Use a shoehorn; never force the heel.</li>
        <li>Store each shoe in its bag, away from direct sunlight and damp.</li>
      </ul>
    </div>

    <!-- Polishing -->
    <div class="tile" style="padding:28px">
      <h2 class="h3">Polishing</h2>
      <ul style="margin:16px 0 0 18px;line-height:1.7">
        <li>Brush off dust with a soft horsehair brush after wearing.</li>
        <li>Feed the leather with a neutral cream every few weeks, then a thin layer of wax on the toe and heel for shine.</li>
        <li><strong>Patina, painted, tattooed and miniature finishes:</strong> use only neutral or matching-tone products, in thin layers. Never use solvents, cleaning wipes or coloured polish on painted areas. For a full restoration of the shine, use our Shoe Shine Service.</li>
      </ul>
    </div>

    <!-- Rain, water and soles -->
    <div class="tile" style="padding:28px">
      <h2 class="h3">Rain, water and soles</h2>
      <ul style="margin:16px 0 0 18px;line-height:1.7">
        <li>If your shoes get wet, blot them with a soft cloth and let them dry at room temperature — never near a heater or in the sun.</li>
        <li>Leather soles take a few wears to settle. A thin rubber protective sole fitted by a good cobbler adds grip and life; ask us if you are unsure.</li>
        <li><strong>Velvet:</strong> brush gently in the direction of the pile with a soft brush; keep away from water.</li>
        <li><strong>Tōramally Green (tyre soles, denim):</strong> wipe with a damp cloth; air-dry.</li>
      </ul>
    </div>
  </div>

  <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px;max-width:860px;margin-bottom:96px">
    <h3 class="h3">Atelier Aftercare &amp; Shine Services</h3>
    <p style="margin-top:8px">When your pair needs deep conditioning, colour re-patina, sole replacement, or artistic touch-up, it can come home to the atelier.</p>
    <div class="row" style="margin-top:20px">
      <a class="btn" href="{{ url('shoe-shine-service') }}">The Shoe Shine Service</a>
      <a class="btn ghost" href="{{ url('restoration') }}">Aftercare &amp; Repairs</a>
    </div>
  </div>
</div>
@endsection
