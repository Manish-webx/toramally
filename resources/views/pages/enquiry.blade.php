@extends('layouts.app')

@section('content')
@php
    if ($kind === 'oneofone') {
        $title = 'One of One';
        $intro = 'Exceptional and collector pieces: a pair made once, from an idea you bring or one we develop together. Heavily painted, carved end to end, or something that has not been made before.';
        $fields = [
            ['name', 'Name', 'text', null, true],
            ['email', 'Email', 'email', null, true],
            ['phone', 'Phone or WhatsApp', 'tel', null, false],
            ['country', 'Country', 'text', null, false],
            ['idea', 'Tell us about the piece', 'textarea', null, true]
        ];
        $art = draw_slot(['shape' => 'adelaide', 'colour' => 'Cognac', 'craft' => 'carving', 'art' => 'snake'], 'A cognac Adelaide Oxford with a carved serpent');
    } else {
        $title = 'Designers & Collectors';
        $intro = 'For fashion designers, retailers, stylists, film and corporate gifting, and collaborations. Tell us what you have in mind and we will send a linesheet or propose a way to work together.';
        $fields = [
            ['name', 'Name', 'text', null, true],
            ['company', 'Company or label', 'text', null, false],
            ['email', 'Email', 'email', null, true],
            ['type', 'Enquiry', 'select', ['Fashion designer', 'Retailer or stockist', 'Stylist', 'Film or production', 'Corporate gifting', 'Collaboration'], true],
            ['linesheet', 'Would you like our linesheet?', 'select', ['Yes', 'No'], false],
            ['message', 'Message', 'textarea', null, true]
        ];
        $art = draw_slot(['shape' => 'wallet', 'colour' => 'Tan', 'craft' => 'tattoo', 'art' => 'mandala'], 'A tan wallet with tattoo line work');
    }
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Bespoke', 'bespoke'], [$title, null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => $title])
  </div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <p>{{ $intro }}</p>
      <div class="art wide" style="margin-top:24px">{!! $art !!}</div>
    </div>
    <div>
      @include('partials.form', ['kind' => $kind, 'fields' => $fields, 'cta' => 'Send enquiry', 'upload' => true])
    </div>
  </div>
</div>
@endsection
