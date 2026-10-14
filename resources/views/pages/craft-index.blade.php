@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Craft', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'The Craft Ladder', 'sub' => 'Seven rungs, from effortless to one of one. The same order, names and promises everywhere in the house.'])
  </div>
  @include('partials.ladder')
</div>

<!-- Techniques of our Atelier (Page 5) -->
<section class="section" id="techniques" style="padding-top:64px;border-top:1px solid var(--line);margin-top:48px"><div class="wrap">
  <div class="head">
    <div>
      <h2 class="h2">Techniques of Our Atelier</h2>
      <p class="muted" style="margin-top:8px;max-width:760px">Every Tōramally begins as plain, uncoated leather. What it becomes depends on the hand that finishes it — patina laid one coat at a time, a design needled into the grain, a miniature painted with mineral pigments, a pattern carved into the surface.</p>
    </div>
  </div>

  <div class="grid g2" style="gap:32px;margin-top:32px">
    <!-- Patina -->
    <div class="tile" style="padding:28px">
      <div style="aspect-ratio:16/9;overflow:hidden;background:#3c2619;margin-bottom:16px;border-radius:4px">{!! macro_slot('patina', 'Cognac') !!}</div>
      <h3 class="h3">Patina</h3>
      <p style="margin-top:12px">Your Tōramallies are hand-painted one coat of colour at a time. Staining full-grain, uncoated, hand-picked leather brings out its natural beauty and the two-tone shading of our signature brushstroke texture.</p>
      <p style="margin-top:8px">We can paint a pair in the colour of your choice, or blend several hues — from black to caramel, azure to sapphire — always with a reflective shine. Shoes should match your personality as much as your clothes, if not more, so we also welcome your own colour for us to develop.</p>
      <div style="margin-top:16px"><a class="tlink" href="{{ url('craft/patina') }}">Explore Patina</a></div>
    </div>

    <!-- Tattooed Leather -->
    <div class="tile" style="padding:28px">
      <div style="aspect-ratio:16/9;overflow:hidden;background:#1f1c19;margin-bottom:16px;border-radius:4px">{!! macro_slot('tattoo', 'Tan') !!}</div>
      <h3 class="h3">Tattooed Leather</h3>
      <p style="margin-top:12px">A tattooed Tōramally carries your personality as surely as a tattoo on your forearm. We take the act of imagining a design and embodying it in your shoes and accessories seriously, because people speak through their tattoos about their most deeply held beliefs.</p>
      <p style="margin-top:8px">Our signature spider-web effect is created on tattooed Tōramallies with a special needle worked directly into the leather. It leaves a tactile texture on the smooth grain that makes each pair one of a kind. Our designers can create a tattoo for you, or reproduce a design you already identify with.</p>
      <div style="margin-top:16px"><a class="tlink" href="{{ url('craft/tattoo') }}">Explore Tattoo</a></div>
    </div>

    <!-- Hand-Painted Miniatures -->
    <div class="tile" style="padding:28px">
      <div style="aspect-ratio:16/9;overflow:hidden;background:#4b1719;margin-bottom:16px;border-radius:4px">{!! macro_slot('miniature', 'Oxblood') !!}</div>
      <h3 class="h3">Hand-Painted Miniatures</h3>
      <p style="margin-top:12px">Miniature painting has been part of Indian heritage since the seventh century AD — a kaleidoscope of imagery painted on palm leaf, wood, ivory, handmade paper, marble, walls, cloth and leather.</p>
      <p style="margin-top:8px">Our artists use fine brushwork and natural pigments — stone dust, 24-carat gold and silver dust — to create these colours, and Tōramally aims to revive miniature painting on leather. Our portfolio ranges from Mughal motifs to swirling dragons to cherry blossoms, and our artists can also create a new work that reflects your own ethos, temperament and spirit.</p>
      <div style="margin-top:16px"><a class="tlink" href="{{ url('craft/miniature') }}">Explore Miniature</a></div>
    </div>

    <!-- Monogram -->
    <div class="tile" style="padding:28px">
      <div style="aspect-ratio:16/9;overflow:hidden;background:#26412f;margin-bottom:16px;border-radius:4px;display:grid;place-items:center;color:#cdb47f;font-family:var(--serif);font-size:36px;letter-spacing:4px">A · S</div>
      <h3 class="h3">Monogram</h3>
      <p style="margin-top:12px">If you identify with your name, your initials or a phrase, your Tōramallies can carry it, hand-painted. A shoe need not only be well designed; it can carry a part of you, literally.</p>
      <p style="margin-top:8px">Choose the monogram option when adding a product to your cart and write the details in the text box. The more you tell us, the better we can understand what you want.</p>
      <div style="margin-top:16px"><a class="tlink" href="{{ url('bespoke/personalisation') }}">Add Monogram in Bespoke</a></div>
    </div>
  </div>

  <div class="grid g2" style="margin-top:40px">
    <div class="notice"><h2 class="h3">Scarring and Tattoo are different crafts</h2><p class="small" style="margin:8px 0 0">Scarring is made by light: a laser removes the finest layer of the grain in a precise, geometric pattern. Tattoo is drawn by hand with a tattoo machine and ink, line by line.</p></div>
    <div class="notice"><h2 class="h3">Add to Bag, or Commission</h2><p class="small" style="margin:8px 0 0">House designs with a fixed price can be added to your bag. Custom artwork, carving and one-of-one pieces begin as a commission, with a design proof before making.</p></div>
  </div>
</div></section>
@endsection
