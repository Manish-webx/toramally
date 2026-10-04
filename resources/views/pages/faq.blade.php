@extends('layouts.app')

@section('content')
@php
    $faq = [
        ['Ordering', 'How do I order?', 'Choose your pair and size, add it to your bag and check out. For custom work, use the builder or book an appointment.'],
        ['Made to order', 'Why does it take weeks?', 'Each pair is made individually in our workshop. Time is part of the process. Most pairs take 5 to 7 weeks; painted and tattooed 6 to 8; carved 10 to 14.'],
        ['Sizing', 'I am between sizes.', 'Choose the larger, or send us your foot measurement on WhatsApp.'],
        ['Care', 'Can patina be changed later?', 'Yes. A patina pair can be taken darker at the house.'],
        ['Shipping', 'Do you ship abroad?', 'Yes, by insured courier. Duties are paid on delivery and we give an estimate before you confirm.'],
        ['Returns', 'Can I exchange?', 'Ready-to-ship pairs, unworn and in the box, can be exchanged for size within 7 days. Personalised and made-to-order pairs are not returnable, and are remade if faulty.'],
        ['Wedding', 'How early should we begin?', 'Ten to fourteen weeks before the date for custom artwork. Use the wedding date check.']
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
  @include('partials.crumbs', ['items' => [['Home', ''], ['FAQ', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Questions'])
  </div>
  <div class="acc" style="max-width:860px;padding-bottom:96px">
    @foreach ($faq as [$g, $q, $a])
      <details>
        <summary>{{ $q }}</summary>
        <div class="in"><p class="label brass">{{ $g }}</p><p>{{ $a }}</p></div>
      </details>
    @endforeach
    <div class="row" style="margin-top:32px">
      <a class="tlink" href="{{ url('shipping') }}">Shipping policy</a>
      <a class="tlink" href="{{ url('returns') }}">Return &amp; exchange policy</a>
    </div>
  </div>
</div>
<script type="application/ld+json">{!! json_encode($ld, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG) !!}</script>
@endsection
