@php
    $nav = $meta['nav'] ?? '';
    $phone = setting('phone');
    $email = setting('email_public');
@endphp
<footer class="site">
  <div class="wrap">
    <div class="cols">
      <div style="grid-column:1/-1">
        <div class="logo" style="justify-self:start;color:var(--ivory-100)"><span data-emblem data-emblem-light></span><span><span class="word">Tōramally</span><span class="desc" style="color:#cdb47f">Bootmaker</span></span></div>
        <p style="margin-top:16px;color:#c9c4b4">Handcrafted in India.</p>
        <h4 style="margin-top:28px">Letters from the House</h4>
        <p class="small" style="color:#c9c4b4">Occasional notes on craft, shoes and the things worth keeping. Early access to limited runs.</p>
        <form class="news" data-newsletter novalidate>
          <label class="vh" for="newsEmail">Email address</label>
          <input id="newsEmail" name="email" type="email" placeholder="Email address" required autocomplete="email">
          <input class="hp hp-input" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
          <button class="btn" type="submit">Subscribe</button>
        </form>
        <p class="small" data-newsmsg aria-live="polite" style="margin-top:8px;color:#cdb47f"></p>
      </div>
      <div><h4>Shop</h4><a href="{{ url('shop/men') }}">Men</a><a href="{{ url('shop/women') }}">Women</a><a href="{{ url('shop/accessories') }}">Accessories</a><a href="{{ url('shop/everyday') }}">Everyday</a><a href="{{ url('shoe-shine-service') }}">Shoe Shine Service</a></div>
      <div><h4>Craft</h4>@foreach (crafts() as $c)<a href="{{ url('craft/' . $c['slug']) }}">{{ $c['name'] }}</a>@endforeach</div>
      <div><h4>Bespoke</h4><a href="{{ url('bespoke/build') }}">Build Your Pair</a><a href="{{ url('bespoke/wedding') }}">Wedding</a><a href="{{ url('bespoke/one-of-one') }}">One of One</a><a href="{{ url('bespoke/designers') }}">Designers &amp; Collectors</a></div>
      <div><h4>Care &amp; Service</h4><a href="{{ url('care') }}">Care</a><a href="{{ url('restoration') }}">Restoration</a><a href="{{ url('size-guide') }}">Size Guide</a><a href="{{ url('shipping') }}">Shipping &amp; Duties</a><a href="{{ url('returns') }}">Returns &amp; Exchange</a><a href="{{ url('faq') }}">FAQ</a></div>
      <div><h4>Contact</h4>
        <a href="{{ setting('maps_url') }}" target="_blank" rel="noopener">Kolkata flagship, Bhowanipore</a>
        @if ($phone)<a href="tel:{{ preg_replace('/\s/', '', $phone) }}">{{ $phone }}</a>@endif
        @if ($email)<a href="mailto:{{ $email }}">{{ $email }}</a>@endif
        <a href="{{ url('appointments') }}">Book an appointment</a>
        <a href="{{ wa_link('Hello Tōramally,') }}" target="_blank" rel="noopener">WhatsApp</a>
        <a href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram @houseoftoramally</a>
        <a href="{{ setting('facebook') }}" target="_blank" rel="noopener">Facebook</a>
      </div>
    </div>
    <div class="legal">
      <span>© {{ date('Y') }} Tōramally. Crafted in silence.</span>
      <span><a href="{{ url('privacy') }}">Privacy</a><a href="{{ url('terms') }}">Terms</a><a href="{{ url('shipping') }}">Shipping</a><a href="{{ url('returns') }}">Returns &amp; Exchange</a><a href="{{ url('cookies') }}">Cookies</a>
      <label class="vh" for="curFoot">Currency</label><select id="curFoot" data-cur style="background:transparent;border:1px solid rgba(156,122,60,.6);color:inherit;height:32px;margin-left:4px">@foreach (array_keys(currencies()) as $c)<option{{ $c === current_currency() ? ' selected' : '' }}>{{ $c }}</option>@endforeach</select></span>
    </div>
  </div>
</footer>

<nav class="tabbar" aria-label="Quick">
  @foreach (['shop' => 'Shop', 'craft' => 'Craft', 'bespoke' => 'Bespoke', 'journal' => 'Journal'] as $k => $t)
    <a href="{{ url($k) }}" aria-current="{{ $nav === $k ? 'true' : 'false' }}">{{ $t }}</a>
  @endforeach
  <button data-open="bagDrawer" aria-controls="bagDrawer">Bag <span class="count" data-bagcount hidden style="top:8px;right:14px">0</span></button>
</nav>

@if (!empty($stickyBuy)) {!! $stickyBuy !!} @endif
<div class="cookie" id="cookie" role="region" aria-label="Cookie notice">
  <span style="flex:1;min-width:200px">We use a few cookies to remember your bag and currency, and, with your consent, to understand how the site is used.</span>
  <button class="btn ghost" data-cookie="decline" style="min-height:40px">Decline</button>
  <button class="btn" data-cookie="accept" style="min-height:40px">Accept</button>
</div>
<div class="toast" id="toast" role="status" aria-live="polite"></div>
