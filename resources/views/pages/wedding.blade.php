@extends('layouts.app')

@section('content')
@php
    $tabs = [
        ['Groom', 'Peshawaris, Belgian loafers and wholecut Oxfords. Miniature painting matched to the sherwani, gold initials and the date inside the heel.', ['rukhsat', 'taus', 'chaman']],
        ['Bride', 'Heels and flats painted to the lehenga. Initials, a motif, or a line of poetry under the sole.', ['gulnar', 'zoya']],
        ['Couples', 'Matching pairs that share one motif, painted to answer each other.', ['taus', 'gulnar']],
        ['Family', 'Pairs for parents and siblings, in one palette. Belts and wallets to match.', ['shaam', 'kamar']],
        ['Wedding Party', 'Pairs for groomsmen and bridesmaids, with one detail in common. Early planning makes this easy.', ['noor', 'makhmal']]
    ];
@endphp

<section class="dark"><div class="wrap split" style="padding-top:40px;padding-bottom:64px">
  <div>
    @include('partials.crumbs', ['items' => [['Home', ''], ['Bespoke', 'bespoke'], ['Wedding', null]], 'light' => true])
    <h1 class="h1" style="margin-top:24px">Made for the day you remember.</h1>
    <p class="muted" style="margin-top:16px">Initials, the date inside the heel, a family motif, colour matched to the outfit, and matching belts and pairs for the family.</p>
  </div>
  <div class="art wide" style="background:#27463a">{!! draw_slot(['shape' => 'loafer', 'colour' => 'Ivory', 'craft' => 'miniature', 'art' => 'botanical', 'initials' => 'S ♥ A', 'gold' => true], 'An ivory wedding loafer with painted flowers and gold initials') !!}</div>
</div></section>

<section class="section"><div class="wrap"><div class="split" style="align-items:start">
  <div>
    <h2 class="h2">Can you make it in time?</h2>
    <p style="margin-top:12px">Enter your date. The answer comes from the workshop's real lead times.</p>
    <label class="field" style="max-width:320px"><span>Wedding date</span><input type="date" data-wdate></label>
    <div data-wans aria-live="polite"></div>
  </div>
  <div>
    <div class="seg" role="tablist" aria-label="Who is it for">
      @foreach ($tabs as $i => [$t])
        <a href="#" role="tab" data-wtab="{{ $i }}" aria-current="{{ $i === 0 ? 'true' : 'false' }}" style="font-size:19px">{{ $t }}</a>
      @endforeach
    </div>
    @foreach ($tabs as $i => [$t, $d, $slugs])
      <div data-wpanel="{{ $i }}"{!! $i ? ' hidden' : '' !!}>
        <p>{{ $d }}</p>
        <div class="grid g2" style="margin-top:20px">
          @foreach ($slugs as $s)
            @php $p = product_by_slug($s); @endphp
            @if ($p)
              @include('partials.product-card', ['p' => $p])
            @endif
          @endforeach
        </div>
      </div>
    @endforeach
  </div>
</div></div></section>

<section class="section panel"><div class="wrap split" style="align-items:start">
  <div>
    <h2 class="h2">Plan your wedding pairs</h2>
    <p style="margin-top:12px">Tell us the date and who the pairs are for. We reply within one working day, by WhatsApp or email, and can meet you in Kolkata.</p>
    @include('partials.wa-button', ['label' => 'Plan your wedding pairs', 'text' => 'Hello Tōramally, we are planning wedding pairs. Our date is: ___. Pairs for: ___.'])
  </div>
  <div>
    @include('partials.form', ['kind' => 'wedding', 'cta' => 'Send enquiry', 'upload' => true, 'fields' => [
      ['name', 'Name', 'text', null, true],
      ['contact', 'Email or WhatsApp number', 'text', null, true],
      ['date', 'Wedding date', 'date', null, true],
      ['for', 'Pairs for', 'select', ['Groom', 'Bride', 'Couple', 'Family', 'Wedding party'], true],
      ['pairs', 'How many pairs', 'number', null, false],
      ['notes', 'Outfit, colours, ideas', 'textarea', null, false]
    ]])
  </div>
</div></section>
@endsection
