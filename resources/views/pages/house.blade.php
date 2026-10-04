@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['The House', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'The House', 'sub' => "Lucknow's memory, Kolkata's spirit, and a small workshop between them."])
  </div>
</div>

<section class="section" id="story"><div class="wrap split">
  <div>
    <h2 class="h2">Our story</h2>
    <div style="margin-top:16px">
      <p>Tōramally was founded by Rahul, who grew up in Uttar Pradesh among one of India's oldest traditions of leathercraft, including the rare practice of inking leather. The founding intention was simple: a house that expresses India's own artistic legacy through the made-to-order shoe, rather than borrowing someone else's.</p>
      <p>The house launched in February 2018 at Lakmé Fashion Week Summer/Resort with hand-welted shoes, belts and wallets, and was among the first in India to tattoo and hand-paint leather footwear. It has grown largely through Instagram and word of mouth, and many of its clients once travelled abroad to find shoes of this kind.</p>
    </div>
  </div>
  <div class="art">{!! draw_slot(['shape' => 'oxford', 'colour' => 'Black', 'craft' => 'patina'], 'A black patina Oxford') !!}</div>
</div></section>

<section class="section" id="lucknow"><div class="wrap split">
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center;background:var(--green-900)"><div style="width:60%"><span data-emblem data-emblem-light></span></div></div></div>
  <div>
    <h2 class="h2">From Lucknow, with memory.</h2>
    <div style="margin-top:16px">
      <p>Miniature painting, Nawabi ornament, the architecture of arches and screens, and a culture of poetry and refinement. Our miniature and tattoo work begins here, and so does our emblem: a fish drawn from the royal Mahi-Maratib insignia.</p>
      <p>The workshop is in Lucknow. Visits are by appointment.</p>
    </div>
  </div>
</div></section>

<section class="section" id="kolkata"><div class="wrap split">
  <div>
    <h2 class="h2">From the city of culture.</h2>
    <div style="margin-top:16px">
      <p>Art, literature, theatre, architecture and fashion. Our flagship is in Bhowanipore, where you can see every craft on the ladder, be measured, and begin a commission.</p>
      <p class="small muted">{{ setting('address_store') }}</p>
      <a class="tlink" href="{{ url('visit') }}">Visit and appointments</a>
    </div>
  </div>
  <div class="art">{!! draw_slot(['shape' => 'mule', 'colour' => 'Cognac', 'craft' => 'patina'], 'A cognac patina mule') !!}</div>
</div></section>

<section class="section" id="workshop"><div class="wrap split">
  <div class="art">{!! macro_slot('patina', 'Oxblood') !!}</div>
  <div>
    <h2 class="h2">A workshop, not a factory.</h2>
    <div style="margin-top:16px">
      <p>A small team of artisans, each with a craft of their own: lasting and welting, patina, painting, tattooing, carving. Our Goodyear-welted pairs carry a fiddle waist, shaped by hand beneath the arch, and a Y-shaped bevel on the sole.</p>
      <p>Colour is never bought in. Each pair is painted coat after coat, which gives the two-tone depth and faint brush texture that mark a Tōramally, and then polished with water and wax, over hours, to a mirror shine.</p>
    </div>
  </div>
</div></section>

<section class="section" id="philosophy"><div class="wrap split">
  <div>
    <h2 class="h2">Philosophy</h2>
    <div style="margin-top:16px">
      <p>Slow luxury. Timeless over seasonal. Handcrafted, with the small imperfections that give a pair its soul. Made to be kept, and when needed, restored. A patina pair can be taken darker years later; bring it back to the house.</p>
    </div>
  </div>
  <div class="art">{!! macro_slot('scarring', 'Dark Brown') !!}</div>
</div></section>

<section class="section" id="materials"><div class="wrap split">
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center"><div data-box style="width:70%"></div></div></div>
  <div>
    <h2 class="h2">Materials</h2>
    <div style="margin-top:16px">
      <p>Calfskin is the house leather. Velvet appears only in the everyday range. Brass for nails and hardware. Every pair is presented in a wooden box, lined in deep green, with botanical motifs.</p>
    </div>
  </div>
</div></section>

<section class="section panel" id="press"><div class="wrap"><div class="head"><h2 class="h2">Press and stockists</h2></div>
  <div class="press">
    @foreach ($press as $x)
      <div><b>{!! !empty($x['url']) ? '<a href="' . e($x['url']) . '" target="_blank" rel="noopener" style="text-decoration:none">' . e($x['name']) . '</a>' : e($x['name']) !!}</b><span class="small muted">{{ $x['note'] }}</span></div>
    @endforeach
  </div>
  <p class="small muted" style="margin-top:24px" id="stockists">Partner stockists in Hyderabad and Bangalore. Message us for the nearest one.</p>
  <div class="row" style="margin-top:16px"><a class="tlink" href="{{ url('house/worn-by') }}">Worn by</a><a class="tlink" href="{{ url('house/videos') }}">Videos</a></div>
</div></section>
@endsection
