@extends('layouts.app')

@section('content')
@php
    $phone = setting('phone');
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Visit & appointments', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Visit the house', 'sub' => 'See every craft on the ladder, be measured, and begin a commission.'])
  </div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <h2 class="h3">Kolkata flagship</h2>
      <p style="margin-top:8px">{{ setting('address_store') }}</p>
      <p class="small muted">{{ setting('hours', "Call or message us for today's hours.") }}</p>
      @if ($phone)
        <p><a href="tel:{{ preg_replace('/\s/', '', $phone) }}">{{ $phone }}</a></p>
      @endif
      <div class="row" style="margin:16px 0 32px">
        <a class="btn ghost" href="{{ setting('maps_url') }}" target="_blank" rel="noopener">Open in Maps</a>
        @include('partials.wa-button', ['label' => 'Message us', 'text' => 'Hello Tōramally,'])
      </div>
      <h2 class="h3">Lucknow workshop</h2>
      <p style="margin-top:8px">By appointment. The address is shared once your visit is confirmed.</p>
      <h2 class="h3" style="margin-top:32px">Stockists</h2>
      <p style="margin-top:8px">Partner stores in Hyderabad and Bangalore. Online at Aashni + Co, Aza Fashions and Tata CLiQ Luxury.</p>
    </div>
    <div id="appointment">
      <h2 class="h3" style="margin-bottom:16px">Book a private appointment</h2>
      @include('partials.form', ['kind' => 'appointment', 'cta' => 'Request appointment', 'fields' => [
        ['name', 'Name', 'text', null, true],
        ['contact', 'Email or WhatsApp number', 'text', null, true],
        ['type', 'Appointment', 'select', ['Bespoke', 'Sizing', 'Collection', 'Wedding', 'Restoration', 'Designer'], true],
        ['location', 'Where', 'select', ['Kolkata flagship', 'Lucknow workshop', 'Video call'], true],
        ['date', 'Preferred date', 'date', null, true],
        ['time', 'Preferred time', 'select', ['Morning', 'Afternoon', 'Evening'], false],
        ['notes', 'Notes', 'textarea', null, false]
      ]])
      <p class="small muted">We confirm by email or WhatsApp with the address and a calendar invitation.</p>
    </div>
  </div>
</div>
@endsection
