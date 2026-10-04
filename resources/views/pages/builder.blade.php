@extends('layouts.app')

@section('content')
@php
    $cfg = [
        'SIL'   => [['Belgian loafer', 'loafer', 16000], ['Penny loafer', 'penny', 17000], ['Mule', 'mule', 14000], ['Oxford', 'oxford', 24000], ['Wholecut Oxford', 'wholecut', 25000], ['Derby', 'derby', 23000], ['Adelaide Oxford', 'adelaide', 24000], ['Belt', 'belt', 7000], ['Wallet', 'wallet', 5000]],
        'CRAFT' => ['patina' => [2000, [4, 6]], 'scarring' => [6000, [5, 7]], 'miniature' => [7000, [6, 8]], 'tattoo' => [8000, [6, 8]], 'carving' => [45000, [10, 14]]],
        'ART'   => ['scarring' => [['lattice', 'House lattice']], 'miniature' => [['botanical', 'Botanical border'], ['peacock', 'Peacock'], ['mahi', 'The house fish']], 'tattoo' => [['mandala', 'Mandala'], ['mahi', 'The house fish']], 'carving' => [['snake', 'Serpent']]],
        'ADDON' => ['initials' => 1500, 'gold' => 2500, 'nails' => 3500, 'custom' => 8000],
        'from'  => $from ? ['sil' => $from['silhouette'], 'colour' => $from['colours'][0]['name'], 'craft' => $from['craft'], 'art' => $from['drawing']['art'] ?? ''] : null,
    ];
@endphp

<div class="wrap" data-builder>
  @include('partials.crumbs', ['items' => [['Home', ''], ['Bespoke', 'bespoke'], ['Build your pair', null]]])
  <div class="builder">
    <div>
      <div class="prev" data-prev aria-live="polite"></div>
      <p class="small muted" style="margin-top:8px">Illustration. Your pair is made by hand; a real swatch photograph and design proof follow for custom work.</p>
    </div>
    <div>
      <div class="prog" data-prog aria-hidden="true"></div>
      <div data-stepbody>
        <noscript><p>The builder needs JavaScript. You can also <a href="{{ url('appointments') }}">book an appointment</a> or message us on WhatsApp.</p></noscript>
      </div>
    </div>
  </div>
</div>
<script type="application/json" id="builderData">{!! json_encode($cfg, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection
