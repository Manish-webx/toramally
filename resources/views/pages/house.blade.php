@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['The House', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'The House', 
      'sub' => "Devoted to the return of handmade luxury — footwear and accessories for the modern, metropolitan individual."
    ])
  </div>
</div>

<section class="section" id="story"><div class="wrap split">
  <div>
    <h2 class="h2">Devoted to Handmade Luxury</h2>
    <div style="margin-top:16px">
      <p>Devoted to the return of handmade luxury, Tōramally offers footwear and accessories for the modern, metropolitan individual — pieces that tick every box on a seasoned connoisseur's list. Each pair is handcrafted with personal care and carries a whiff of an era gone by: the age of steam engines, cigars and elegantly dressed gentlemen.</p>
      <p>Our shoes, known as Tōramallies, draw on the craftsmanship of the early 1900s and are made for well-heeled men and women who value a well-made pair. Cut from fine-grained leather, every Tōramally is meticulously finished to stand the test of time.</p>
      <p>The Tōramally story is not complete without the reason we exist. We give talented artisans a canvas on which to show the world their skill while earning their livelihood. Today we work with a small team of artisans; in time, we intend to build a community that supports many more.</p>
    </div>
  </div>
  <div class="art">{!! draw_slot(['shape' => 'oxford', 'colour' => 'Black', 'craft' => 'patina'], 'A black patina Oxford') !!}</div>
</div></section>

<section class="section panel" id="artisans"><div class="wrap split">
  <div class="art">{!! macro_slot('patina', 'Oxblood') !!}</div>
  <div>
    <h2 class="h2">Artisans &amp; the Art</h2>
    <div style="margin-top:16px">
      <p>Tōramally is nothing without its artisans and their devotion to their craft. They put heart and soul into every pair, working through each of some 300 processes, one by one, to give you your Tōramallies.</p>
      <p>Armed with brushes, sponges and old cloths, they revive an era that is slowly slipping away beneath machine-made, cookie-cutter design.</p>
    </div>
  </div>
</div></section>

<section class="section" id="lucknow"><div class="wrap split">
  <div>
    <h2 class="h2">From Lucknow, with memory.</h2>
    <div style="margin-top:16px">
      <p>Miniature painting, Nawabi ornament, the architecture of arches and screens, and a culture of poetry and refinement. Our miniature and tattoo work begins here, and so does our emblem: a fish drawn from the royal Mahi-Maratib insignia.</p>
      <p>The workshop is in Lucknow. Visits are by appointment.</p>
    </div>
  </div>
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center;background:var(--green-900)"><div style="width:60%"><span data-emblem data-emblem-light></span></div></div></div>
</div></section>

<section class="section" id="kolkata"><div class="wrap split">
  <div class="art">{!! draw_slot(['shape' => 'mule', 'colour' => 'Cognac', 'craft' => 'patina'], 'A cognac patina mule') !!}</div>
  <div>
    <h2 class="h2">From the city of culture.</h2>
    <div style="margin-top:16px">
      <p>Art, literature, theatre, architecture and fashion. Our flagship is in Bhowanipore, where you can see every craft on the ladder, be measured, and begin a commission.</p>
      <p class="small muted">{{ setting('address_store') }}</p>
      <a class="tlink" href="{{ url('visit') }}">Visit and appointments</a>
    </div>
  </div>
</div></section>

<section class="section" id="workshop"><div class="wrap split">
  <div>
    <h2 class="h2">A workshop, not a factory.</h2>
    <div style="margin-top:16px">
      <p>A small team of artisans, each with a craft of their own: lasting and welting, patina, painting, tattooing, carving. Our Goodyear-welted pairs carry a fiddle waist, shaped by hand beneath the arch, and a Y-shaped bevel on the sole.</p>
      <p>Colour is never bought in. Each pair is painted coat after coat, which gives the two-tone depth and faint brush texture that mark a Tōramally, and then polished with water and wax, over hours, to a mirror shine.</p>
    </div>
  </div>
  <div class="art">{!! macro_slot('scarring', 'Dark Brown') !!}</div>
</div></section>

<section class="section panel" id="green"><div class="wrap split">
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center;background:#1e3427;color:var(--ivory-100);padding:32px;text-align:center"><div class="serif-i" style="font-size:32px;line-height:1.2;color:#cdb47f">Tōramally Green</div><p class="small" style="margin-top:12px;opacity:0.9">Residual leather, discarded denim and scrap tyre soles.</p></div></div>
  <div>
    <h2 class="h2">Tōramally Green</h2>
    <div style="margin-top:16px">
      <p>It began with the pandemic in 2020. The pause made us look at ourselves from the other side — not only as a business or a brand, but as a house responsible for other people's livelihoods. We began reading about product development, upcycling, vulcanisation and consumerism. As our founder and creative head, Rahul Shastri, puts it: <em>“Being conscious and aware is one thing. Actually trying to find a solution, even in the smallest of ways, is another.”</em></p>
      <p>While working through ideas, Rahul came upon a small tyre-puncture shop that was also home to Rehman and his family. Rehman, a mechanic who repaired tyres and serviced cars, had lost his entire income in the lockdown. Rahul sat with him to see whether a loosely formed idea could become something concrete — and, above all, something sustainable.</p>
      <p>Rehman was moved to tears when Rahul offered to pay him for his work, and he was determined to make the project succeed, for his family depended on it. Together we tested scrap tyres and contoured them into soles.</p>
      <p>Today, after months of work, Tōramally presents shoes repurposed from residual leather, discarded denim and scrap tyres. They are practical, comfortable and kinder to the planet, while remaining luxurious. Most of all, they open honest conversations about words now used loosely — sustainable, organic, carbon-neutral. Every pair of Tōramallies starts that conversation, and invites its owner to carry it on.</p>
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
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center"><div data-box style="width:70%"></div></div></div>
</div></section>

<section class="section" id="materials"><div class="wrap split">
  <div class="art"><div style="width:100%;height:100%;display:grid;place-items:center;background:var(--green-900)"><div style="width:60%"><span data-emblem data-emblem-light></span></div></div></div>
  <div>
    <h2 class="h2">Materials</h2>
    <div style="margin-top:16px">
      <p>Calfskin is the house leather. Velvet appears only in the everyday range. Brass for nails and hardware. Every pair travels in our screen-printed pine-wood box, wrapped in organic cloth, with shoe bags to guard against scuffs and scratches.</p>
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
