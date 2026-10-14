@extends('layouts.app')

@section('content')
@php
    $faq = [
        [
            'Made to order',
            'Are Tōramallies made to order?',
            'Some pairs are held ready in our most-requested sizes; these ship within days. Everything else, and anything personalised, is made for you in 45–90 days.'
        ],
        [
            'Sizing',
            'How do I know my size?',
            'See our Size & Fit Guide, or write to us with your usual size in another brand — we will advise for the last you have chosen.'
        ],
        [
            'Returns & Exchanges',
            'Can I return a pair?',
            'Ready pairs, unworn, within 7 days of delivery. Made-to-order, personalised and bespoke pieces cannot be returned. See Returns, Exchanges & Cancellations.'
        ],
        [
            'Handcrafted Character',
            'Why does my pair look slightly different from the photograph?',
            'Every finish is done by hand on natural leather, so tone and brushwork vary from pair to pair. That is what makes yours one of a kind.'
        ],
        [
            'Shipping & Duties',
            'Do you ship internationally?',
            'Yes, by DHL Express to most countries. See Shipping & Delivery for times and duties.'
        ],
        [
            'Atelier & Stores',
            'Can I visit you?',
            'Our flagship is in Bhowanipore, Kolkata, and our atelier is in Lucknow. We recommend booking an appointment for bespoke consultations.'
        ],
        [
            'Weddings',
            'Can you make wedding footwear?',
            'Yes. Wedding pairs for the groom and bride are among our most-loved commissions. Allow at least three months before the date.'
        ],
        [
            'Custom Artwork',
            'Can you paint or tattoo a design I already have?',
            'Yes. Upload a reference image when you order, or share it in your consultation. We will send a design for approval before we begin.'
        ],
        [
            'Patrons',
            'What are Patrons?',
            'Clients who have collected several pairs receive a Patron Benefit on future purchases. See the Patrons page.'
        ],
        [
            'Owners Register',
            'I already own Tōramallies. Can I register them?',
            'Yes — register your pairs in the Owners\' Register to join our Patron programme.'
        ],
        [
            'Aftercare & Repairs',
            'Can my shoes be resoled?',
            'Hand-welted and hand-stitched Blake pairs can be resoled at our atelier; cemented pairs are assessed case by case. See Aftercare & Repairs.'
        ],
        [
            'Support & Grievances',
            'How do I raise a complaint?',
            'Write to care@toramally.com or our Grievance Officer. You will receive a ticket number and an acknowledgement within 48 hours.'
        ],
    ];

    $ld = [
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => array_map(fn($f) => [
            '@type' => 'Question',
            'name' => $f[1],
            'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[2]]
        ], $faq)
    ];
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['FAQs', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'Frequently Asked Questions',
      'sub' => 'Everything you need to know about our making, sizing, delivery, care and bespoke commissions.'
    ])
  </div>
  <div class="acc" style="max-width:860px;padding-bottom:96px">
    @foreach ($faq as $i => [$g, $q, $a])
      <details{!! $i === 0 ? ' open' : '' !!}>
        <summary>{{ $q }}</summary>
        <div class="in"><p class="label brass">{{ $g }}</p><p>{{ $a }}</p></div>
      </details>
    @endforeach
    <div class="row" style="margin-top:36px;gap:20px">
      <a class="tlink" href="{{ url('shipping') }}">Shipping &amp; Delivery policy</a>
      <a class="tlink" href="{{ url('returns') }}">Returns, Exchanges &amp; Cancellations policy</a>
      <a class="tlink" href="{{ url('size-guide') }}">Size &amp; Fit Guide</a>
      <a class="tlink" href="{{ url('contact') }}">Contact Client Care</a>
    </div>
  </div>
</div>
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection
