@php
    $nav = $meta['nav'] ?? '';
    $cur = current_currency();
    $curOptions = implode('', array_map(fn($c) => '<option' . ($c === $cur ? ' selected' : '') . '>' . e($c) . '</option>', array_keys(currencies())));
    $crafts = crafts();
    $feature = product_by_slug('taus') ?? (featured_products(1)[0] ?? null);
    $customer = auth()->user();
@endphp
<a class="skip" href="#app">Skip to content</a>
@if (setting('announcement'))
<div class="announce" id="announce" hidden><span>{{ setting('announcement') }}</span><button aria-label="Dismiss notice" id="announceX">✕</button></div>
@endif

<header class="site" id="hdr">
  <div class="wrap hbar">
    <button class="icon-btn menu-btn" aria-label="Open menu" aria-controls="menuDrawer" aria-expanded="false" data-open="menuDrawer">
      <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M3 7h18M3 12h18M3 17h18"/></svg>
    </button>
    <a class="logo" href="{{ url('/') }}" aria-label="Tōramally, home">
      <span data-emblem></span>
      <span><span class="word">Tōramally</span><span class="desc">Bootmaker</span></span>
    </a>
    <nav class="main" aria-label="Main">
      <div><a class="top" href="{{ url('shop') }}" aria-current="{{ $nav === 'shop' ? 'true' : 'false' }}">Shop</a>
        <div class="mega"><div class="wrap">
          @if (is_category_active('Men'))
          <div><h4>Men</h4>
            <a href="{{ url('shop/men/classic') }}">Classic</a><a href="{{ url('shop/men/special-occasion') }}">Special Occasion</a>
            @foreach (['Belgian loafer', 'Penny loafer', 'Wholecut Oxford', 'Oxford', 'Adelaide Oxford', 'Derby', 'Mule', 'Peshawari'] as $s)
              <a href="{{ url('shop/men/' . Str::slug($s) . 's') }}">{{ $s }}s</a>
            @endforeach
          </div>
          @endif
          @if (is_category_active('Women') || is_category_active('Everyday'))
          <div>
            @if (is_category_active('Women'))
              <h4>Women</h4><a href="{{ url('shop/women/heels') }}">Heels</a><a href="{{ url('shop/women/flats') }}">Flats</a><a href="{{ url('shop/women/mules') }}">Mules</a>
            @endif
            @if (is_category_active('Everyday'))
              <h4 style="{{ is_category_active('Women') ? 'margin-top:24px' : '' }}">Everyday</h4><a href="{{ url('shop/everyday?craft=velvet') }}">Velvet</a><a href="{{ url('shop/everyday/slippers') }}">Slippers</a>
            @endif
          </div>
          @endif
          @if (is_category_active('Accessories') || is_category_active('Service'))
          <div>
            @if (is_category_active('Accessories'))
              <h4>Accessories</h4><a href="{{ url('shop/accessories/belts') }}">Belts</a><a href="{{ url('shop/accessories/wallets') }}">Wallets</a><a href="{{ url('shop/accessories/collectibles') }}">Collectibles</a>
            @endif
            @if (is_category_active('Service'))
              <h4 style="{{ is_category_active('Accessories') ? 'margin-top:24px' : '' }}">Services</h4><a href="{{ url('shoe-shine-service') }}">The Shoe Shine Service</a>
            @endif
          </div>
          @endif
          <div><h4>Collections</h4>
            @foreach (editorial_collections() as $c)
              <a href="{{ url('shop?collection=' . $c['slug']) }}">{{ $c['name'] }}</a>
            @endforeach
            <h4 style="margin-top:24px">By availability</h4><a href="{{ url('shop?avail=Ready+to+Ship') }}">Ready to Ship</a><a href="{{ url('shop?avail=Made+to+Order') }}">Made to Order</a></div>
          <div class="feat">@if ($feature)
            <a href="{{ product_url($feature) }}" style="text-decoration:none"><div style="background:var(--ivory-100)">{!! product_visual($feature) !!}</div>
            <div style="font-family:var(--serif);font-size:22px;margin-top:8px">{{ $feature['name'] }}</div><div class="small muted">{{ $feature['silhouette'] . ', ' . $feature['craft_name'] }}</div></a>
          @endif</div>
        </div></div>
      </div>
      <div><a class="top" href="{{ url('craft') }}" aria-current="{{ $nav === 'craft' ? 'true' : 'false' }}">Craft</a>
        <div class="mega"><div class="wrap" style="grid-template-columns:repeat({{ count($crafts) }},1fr)">
          @foreach ($crafts as $c)
            <a href="{{ url('craft/' . $c['slug']) }}" style="text-decoration:none"><div style="aspect-ratio:1;overflow:hidden">{!! macro_slot($c['slug'], craft_colour($c['slug'])) !!}</div>
            <div style="font-family:var(--serif);font-size:20px;margin-top:8px">{{ $c['name'] }}</div><div class="small brass">{{ $c['tagline'] }}</div></a>
          @endforeach
        </div></div>
      </div>
      <div><a class="top" href="{{ url('bespoke') }}" aria-current="{{ $nav === 'bespoke' ? 'true' : 'false' }}">Bespoke</a>
        <div class="mega"><div class="wrap">
          <div><h4>Make it yours</h4><a href="{{ url('bespoke/build') }}">Build Your Pair</a><a href="{{ url('bespoke/custom-colour') }}">Custom Colour</a><a href="{{ url('bespoke/personalisation') }}">Personalisation</a><a href="{{ url('bespoke/custom-artwork') }}">Custom Artwork</a></div>
          <div><h4>Occasions</h4><a href="{{ url('bespoke/wedding') }}">Wedding</a><a href="{{ url('bespoke/one-of-one') }}">One of One</a><a href="{{ url('bespoke/designers') }}">Designers &amp; Collectors</a></div>
          <div><h4>Meet us</h4><a href="{{ url('appointments') }}">Book an Appointment</a><a href="{{ url('visit') }}">Visit Kolkata</a></div>
          <div></div>
          <div class="feat"><p class="serif-i" style="font-size:26px;line-height:32px;margin-bottom:12px">You do not simply choose a Tōramally. You decide how much of yourself goes into it.</p><a class="tlink" href="{{ url('bespoke/build') }}">Begin your commission</a></div>
        </div></div>
      </div>
      <div><a class="top" href="{{ url('house') }}" aria-current="{{ $nav === 'house' ? 'true' : 'false' }}">The House</a>
        <div class="mega"><div class="wrap">
          <div><h4>Origins</h4><a href="{{ url('house/story') }}">Our Story</a><a href="{{ url('house/lucknow') }}">Lucknow</a><a href="{{ url('house/kolkata') }}">Kolkata</a></div>
          <div><h4>The work</h4><a href="{{ url('house/workshop') }}">The Workshop</a><a href="{{ url('house/philosophy') }}">Philosophy</a><a href="{{ url('house/materials') }}">Materials</a></div>
          <div><h4>Proof</h4><a href="{{ url('house/worn-by') }}">Worn By</a><a href="{{ url('house/press') }}">Press &amp; Stockists</a><a href="{{ url('house/videos') }}">Videos</a></div>
          <div><h4>Visit</h4><a href="{{ url('visit') }}">Kolkata flagship</a><a href="{{ url('contact') }}">Contact</a></div>
          <div class="feat"><p class="serif-i" style="font-size:26px;line-height:32px;margin-bottom:12px">A workshop, not a factory.</p><a class="tlink" href="{{ url('house/workshop') }}">Inside the workshop</a></div>
        </div></div>
      </div>
      <div><a class="top" href="{{ url('journal') }}" aria-current="{{ $nav === 'journal' ? 'true' : 'false' }}">Journal</a></div>
    </nav>
    <div class="hright">
      <button class="icon-btn" aria-label="Search" data-open-search>
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.5 15.5 21 21"/></svg>
      </button>
      <label class="vh" for="curSel">Currency</label>
      <select class="cur" id="curSel" data-cur>{!! $curOptions !!}</select>
      
      <!-- Customer Account / Sign-In Button -->
      <a class="icon-btn hide-m" href="{{ $customer ? route('account') : route('login') }}" aria-label="{{ $customer ? 'My Account' : 'Sign In' }}" title="{{ $customer ? 'My Account (' . $customer->first_name . ')' : 'Sign In' }}">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><circle cx="12" cy="8" r="4"/><path d="M4 21c1.5-4 4.5-6 8-6s6.5 2 8 6"/></svg>
      </a>

      <button class="icon-btn" aria-label="Bag" data-open="bagDrawer" aria-controls="bagDrawer">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.3"><path d="M5 8h14l-1 13H6L5 8z"/><path d="M9 8V6a3 3 0 0 1 6 0v2"/></svg>
        <span class="count" data-bagcount hidden>0</span>
      </button>
      <a class="btn ghost cta-top" href="{{ url('bespoke/build') }}">Commission a pair</a>
    </div>
  </div>
</header>

<div class="search-panel" id="searchPanel" role="dialog" aria-modal="true" aria-label="Search">
  <div class="wrap">
    <form class="row" style="justify-content:space-between;flex-wrap:nowrap" action="{{ url('search') }}" method="get" role="search">
      <label class="vh" for="q">Search Tōramally</label>
      <input id="q" name="q" type="search" autocomplete="off" placeholder="Search pairs, crafts, stories">
      <button type="button" class="icon-btn" aria-label="Close search" data-close-search>✕</button>
    </form>
    <div class="sug">
      @foreach (['Belgian', 'Miniature', 'Wedding', 'Velvet', 'Scarring', 'Initials'] as $s)
        <button class="chip" data-s="{{ $s }}">{{ $s }}</button>
      @endforeach
    </div>
    <div class="sres" data-sres aria-live="polite"></div>
  </div>
</div>

<div class="scrim" id="scrim"></div>

<aside class="drawer left" id="menuDrawer" aria-label="Menu" aria-hidden="true">
  <div class="dhead"><span class="logo" style="justify-self:start"><span data-emblem></span><span><span class="word">Tōramally</span><span class="desc">Bootmaker</span></span></span><button class="icon-btn" aria-label="Close menu" data-close>✕</button></div>
  <div class="dbody dnav">
    <div style="padding-bottom:16px;margin-bottom:16px;border-bottom:1px solid var(--line)">
      @if ($customer)
        <a href="{{ route('account') }}" style="font-weight:500;color:var(--green-900)">My Account ({{ $customer->first_name }})</a>
      @else
        <div class="row" style="gap:12px">
          <a href="{{ route('login') }}" class="btn ghost small" style="flex:1;text-align:center">Sign In</a>
          <a href="{{ route('register') }}" class="btn small" style="flex:1;text-align:center">Register</a>
        </div>
      @endif
    </div>
    <details><summary>Shop</summary>
      @if (is_category_active('Men'))
        <a href="{{ url('shop/men') }}">Men</a><a href="{{ url('shop/men/classic') }}">&nbsp;&nbsp;Classic</a><a href="{{ url('shop/men/special-occasion') }}">&nbsp;&nbsp;Special Occasion</a>
      @endif
      @if (is_category_active('Women'))
        <a href="{{ url('shop/women') }}">Women</a>
      @endif
      @if (is_category_active('Accessories'))
        <a href="{{ url('shop/accessories') }}">Accessories</a>
      @endif
      @if (is_category_active('Everyday'))
        <a href="{{ url('shop/everyday') }}">Everyday</a>
      @endif
      @if (is_category_active('Service'))
        <a href="{{ url('shoe-shine-service') }}">The Shoe Shine Service</a>
      @endif
    </details>
    <details><summary>Craft</summary>
      @foreach ($crafts as $c)
        <a href="{{ url('craft/' . $c['slug']) }}">{{ $c['name'] }}</a>
      @endforeach
    </details>
    <details><summary>Bespoke</summary><a href="{{ url('bespoke/build') }}">Build Your Pair</a><a href="{{ url('bespoke') }}">Custom Colour, Personalisation, Artwork</a><a href="{{ url('bespoke/wedding') }}">Wedding</a><a href="{{ url('bespoke/one-of-one') }}">One of One</a><a href="{{ url('bespoke/designers') }}">Designers &amp; Collectors</a></details>
    <details><summary>The House</summary><a href="{{ url('house/story') }}">Our Story</a><a href="{{ url('house/lucknow') }}">Lucknow</a><a href="{{ url('house/kolkata') }}">Kolkata</a><a href="{{ url('house/workshop') }}">The Workshop</a><a href="{{ url('house/philosophy') }}">Philosophy</a><a href="{{ url('house/worn-by') }}">Worn By</a><a href="{{ url('house/press') }}">Press &amp; Stockists</a><a href="{{ url('house/videos') }}">Videos</a></details>
    <a class="plain" href="{{ url('journal') }}">Journal</a>
    <div style="padding-top:20px">
      <a href="{{ url('care') }}">Care</a><a href="{{ url('restoration') }}">Restoration</a><a href="{{ url('size-guide') }}">Size Guide</a><a href="{{ url('shipping') }}">Shipping &amp; Duties</a><a href="{{ url('returns') }}">Returns &amp; Exchange</a><a href="{{ url('visit') }}">Visit &amp; Appointments</a><a href="{{ url('contact') }}">Contact</a><a href="{{ url('faq') }}">FAQ</a>
    </div>
    <label class="field" style="margin-top:20px"><span>Currency</span><select data-cur>{!! $curOptions !!}</select></label>
    @include('partials.wa-button', ['label' => 'Message us on WhatsApp', 'text' => 'Hello Tōramally,', 'class' => 'btn ghost full'])
  </div>
</aside>

<aside class="drawer right" id="bagDrawer" aria-label="Your bag" aria-hidden="true">
  <div class="dhead"><h2 class="h3">Your bag</h2><button class="icon-btn" aria-label="Close bag" data-close>✕</button></div>
  <div class="dbody" data-bagbody></div>
  <div class="dfoot" data-bagfoot></div>
</aside>

<aside class="sheet" id="filterSheet" aria-label="Filters" aria-hidden="true">
  <div class="dhead" style="padding:12px 0"><h2 class="h3">Filter</h2><button class="icon-btn" aria-label="Close filters" data-close>✕</button></div>
  <div data-sheetfilters></div>
  <button class="btn full" data-close style="margin-top:16px">Show pairs</button>
</aside>
