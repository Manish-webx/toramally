@extends('layouts.app')

@section('content')
@php
    $COL = ['Black' => '#1f1c19', 'Oxblood' => '#4b1719', 'Burgundy' => '#5e1f2c', 'Tobacco' => '#6a4528', 'Cognac' => '#8f4b21', 'Dark Brown' => '#3c2619', 'Tan' => '#a66e3d', 'Forest Green' => '#26412f', 'Navy' => '#1e2b45', 'Beige' => '#c9b28f', 'Ivory' => '#e9e0cc'];
@endphp

<section class="dark"><div class="wrap split" style="padding-top:48px;padding-bottom:72px">
  <div>
    @include('partials.crumbs', ['items' => [['Home', ''], ['Bespoke', null]], 'light' => true])
    <h1 class="h1" style="margin-top:24px">Bespoke: Made for you.</h1>
    <p class="muted" style="margin:16px 0 28px">Tōramally will help you create a piece that reflects your individuality. A simple conversation is enough for us to agree on even the most intricate design. Choose a silhouette, colour, craft and artwork, with live estimates and personal fitting.</p>
    <div class="row">
      <a class="btn" href="{{ url('bespoke/build') }}" data-track="begin_commission">Begin your commission</a>
      <a class="btn ghost" href="{{ url('appointments') }}">Book an appointment</a>
    </div>
  </div>
  <div class="art" style="background:#27463a">{!! draw_slot(['shape' => 'oxford', 'colour' => 'Navy', 'craft' => 'miniature', 'art' => 'botanical', 'initials' => 'A S', 'gold' => true], 'A navy Oxford with botanical painting and gold initials') !!}</div>
</div></section>

<!-- How a Commission Unfolds (Page 4) -->
<section class="section" id="process"><div class="wrap">
  <div class="head">
    <div>
      <h2 class="h2">How a Commission Unfolds</h2>
      <p class="muted" style="margin-top:8px">From initial conversation to lasting heirloom.</p>
    </div>
  </div>

  <div class="grid g2" style="gap:32px;margin-top:24px">
    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3">The Beginning</h3>
      <p style="margin-top:12px">It begins with listening. We first reflect on your wishes and your taste, then bring our own know-how to the conversation. To begin a commission we ask for your shoe size in EU, UK and US, and any preference on toe width. Explore our shoes and design elements to understand our language better.</p>
    </div>

    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3">Development</h3>
      <p style="margin-top:12px">Every pattern is made for the individual. Because each client's feet and wishes differ, we record your sizing and detail it accordingly. The leather is cut to that pattern, and every piece is assembled and sewn by hand. Our craftsmen leave nothing to chance.</p>
    </div>

    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3">Trials</h3>
      <p style="margin-top:12px">Reserved for our exclusive commissions, this service sends you two trial pairs, a size apart, so that fit and comfort can be judged on your own feet. Once you confirm the fit, the trial pairs return to us, and your shoes are finished or adjusted accordingly. Settling the fit before the final pair is made is why our clients keep coming back.</p>
    </div>

    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3">Delivery</h3>
      <p style="margin-top:12px">A pair of Tōramallies takes between 45 and 90 days in our artisans' hands. Every pair is shipped in a screen-printed pine-wood box, wrapped in organic cloth, with shoe bags to guard against scuffs and scratches.</p>
    </div>
  </div>

  <div style="margin-top:32px;background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
    <h3 class="h3">Finishing: Hand-Welted, Blake &amp; Cemented</h3>
    <div style="margin-top:14px">
      <p>Every Tōramally is built in one of three ways:</p>
      <ul style="margin:12px 0 16px 20px;line-height:1.7">
        <li><strong>Hand-welted pairs</strong> — our finest — have the welt stitched to the upper by hand, passing through more than 300 labour-intensive processes for greater support and lasting durability.</li>
        <li><strong>Hand-stitched Blake pairs</strong> have the sole stitched directly to the upper by hand, for a closer, lighter and more flexible shoe.</li>
        <li><strong>Cemented pairs</strong>, such as our velvet line, have the sole bonded to the upper, for a light, easy shoe.</li>
      </ul>
      <p>Our hand-welted shoes carry a characteristic bevel at the waist of the sole, known as the <em>fiddle back</em>. First used by English shoemakers, this bespoke finish strengthens the outsole and adds to its elegance. Depending on the design, our hand-stitched Blake pairs may also be finished with a narrow waist.</p>
      <p style="margin-top:12px">Depending on the order, the shoes are then hand-painted or tattooed. Tattooing adds a tactile quality to a smooth surface, while hand-painting brings out the rich two-tone shading of our signature brushstroke texture. Hours, then days, are spent building up the shine in layers of water and wax, giving every Tōramally its characteristic mirror shine.</p>
    </div>
    <div class="row" style="margin-top:20px">
      <a class="tlink" href="{{ url('bespoke-terms') }}">Read Bespoke &amp; Made-to-Order Terms</a>
    </div>
  </div>
</div></section>

<section class="section" id="custom-colour"><div class="wrap split">
  <div><h2 class="h2">Custom colour</h2><p style="margin-top:16px">Eleven colours in the house library, each built by hand in layers. Or bring your own: upload a reference or give us a colour code, and we send a photograph of a real swatch for your approval before making, because screens vary.</p><a class="tlink" href="{{ url('bespoke/build') }}">Choose a colour</a></div>
  <div class="grid" style="grid-template-columns:repeat(4,1fr);gap:8px">
    @foreach ($COL as $n => $h)
      <div><div style="aspect-ratio:1;background:{{ $h }}">{!! macro_slot('patina', $n, $n . ' patina swatch') !!}</div><div class="small" style="margin-top:4px">{{ $n }}</div></div>
    @endforeach
  </div>
</div></section>

<section class="section panel" id="personalisation"><div class="wrap split">
  <div class="art wide">{!! draw_slot(['shape' => 'loafer', 'colour' => 'Tan', 'craft' => 'patina', 'initials' => 'M R', 'gold' => true, 'nails' => true], 'A tan loafer with gold initials and a brass nail monogram') !!}</div>
  <div><h2 class="h2">Personalisation &amp; Monogram</h2><p style="margin-top:16px">If you identify with your name, your initials or a phrase, your Tōramallies can carry it, hand-painted. A shoe need not only be well designed; it can carry a part of you, literally.</p><p style="margin-top:8px">Initials painted on the heel or insole, initials in gold leaf, or a monogram set in brass nails on the sole. Up to three letters. You see them on your pair as you type.</p><a class="tlink" href="{{ url('bespoke/build') }}">Add your initials or monogram</a></div>
</div></section>

<section class="section" id="custom-artwork"><div class="wrap split">
  <div><h2 class="h2">Custom artwork</h2><p style="margin-top:16px">Choose from the house library of miniatures, tattoo and scarring designs, or bring a sketch, a family motif or the embroidery from an outfit. We prepare a digital proof and quote before making.</p><a class="tlink" href="{{ url('bespoke/build?craft=miniature') }}">Start with artwork</a></div>
  <div class="grid g3" style="gap:8px">
    @foreach (['miniature', 'tattoo', 'carving'] as $k)
      <div class="art" style="aspect-ratio:1">{!! macro_slot($k, craft_colour($k)) !!}</div>
    @endforeach
  </div>
</div></section>

<section class="section panel"><div class="wrap"><div class="head"><h2 class="h2">For occasions and collaborations</h2></div>
  <div class="grid g3">
    <a class="tile" href="{{ url('bespoke/wedding') }}"><h3 class="h3">Wedding</h3><p class="small muted">Pairs for the groom, the bride and the family. Check your date.</p></a>
    <a class="tile" href="{{ url('bespoke/one-of-one') }}"><h3 class="h3">One of One</h3><p class="small muted">Exceptional and collector pieces, made once.</p></a>
    <a class="tile" href="{{ url('bespoke/designers') }}"><h3 class="h3">Designers &amp; Collectors</h3><p class="small muted">Designers, stylists, retailers, film and corporate gifting.</p></a>
  </div></div></section>
@endsection
