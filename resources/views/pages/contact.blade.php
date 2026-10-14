@extends('layouts.app')

@section('content')
@php
    $phone = setting('phone', '+91 99195 67356');
    $email = setting('email_public', 'care@toramally.com');
    $hours = setting('hours', 'Monday to Saturday, 11:00–19:00 IST');
    $storeAddress = setting('address_store', 'Ground Floor, 26 Chakraberia Road North, Jadubabur Bazar, Bhowanipore, Kolkata');
    $atelierAddress = setting('business_address', '595-AA/050-A, 2nd Floor, beside Olive Crown Hotel, Swaroop Chandra Khera, LDA Colony, Kanpur Road, Lucknow 226012');
    $proprietor = setting('proprietor', 'Rahul Shastri');
    $grievanceEmail = setting('grievance_email', 'grievance@toramally.com');
    $gstin = setting('gstin');
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Contact', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'Contact the House',
      'sub' => 'House of Tōramally · Bootmaker'
    ])
  </div>

  <div class="split" style="align-items:start;padding-bottom:96px;gap:48px">
    <div>
      <div style="margin-bottom:28px">
        <h2 class="h3">House of Tōramally</h2>
        <p class="small muted" style="margin-top:4px">Proprietor: {{ $proprietor }}</p>
      </div>

      <div style="margin-bottom:24px">
        <h3 class="h4" style="font-family:var(--serif);font-size:18px;margin-bottom:6px">Atelier &amp; Registered Office</h3>
        <p class="small" style="line-height:1.6;color:var(--ink-700)">{{ $atelierAddress }}</p>
      </div>

      <div style="margin-bottom:24px">
        <h3 class="h4" style="font-family:var(--serif);font-size:18px;margin-bottom:6px">Flagship Store</h3>
        <p class="small" style="line-height:1.6;color:var(--ink-700)">{{ $storeAddress }}</p>
        <div style="margin-top:8px">
          <a class="tlink" href="{{ setting('maps_url') }}" target="_blank" rel="noopener">Open store in Google Maps</a>
        </div>
      </div>

      <div style="margin-bottom:24px;border-top:1px solid var(--line);padding-top:20px">
        <h3 class="h4" style="font-family:var(--serif);font-size:18px;margin-bottom:6px">Client Care</h3>
        <p class="small" style="line-height:1.8">
          <strong>Phone / WhatsApp:</strong> <a href="tel:{{ preg_replace('/\s/', '', $phone) }}">{{ $phone }}</a><br>
          <strong>Email:</strong> <a href="mailto:{{ $email }}">{{ $email }}</a><br>
          <strong>Hours:</strong> {{ $hours }}
        </p>
        <div class="row" style="margin-top:14px">
          @include('partials.wa-button', ['label' => 'WhatsApp Client Care', 'text' => 'Hello Tōramally,'])
          <a class="btn ghost" href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram</a>
        </div>
      </div>

      <div style="background:var(--ivory-100,#f7f5f0);padding:20px;border-radius:4px;margin-top:28px">
        <h3 class="h4" style="font-family:var(--serif);font-size:17px;margin-bottom:6px">Grievance Officer</h3>
        <p class="small" style="line-height:1.6">
          <strong>Email:</strong> <a href="mailto:{{ $grievanceEmail }}">{{ $grievanceEmail }}</a>
        </p>
        <p class="small muted" style="margin-top:8px">Every complaint receives a ticket number. We acknowledge it within 48 hours and resolve it within one month.</p>
        @if ($gstin)
          <p class="small muted" style="margin-top:8px"><strong>GSTIN:</strong> {{ $gstin }}</p>
        @endif
      </div>
    </div>

    <div>
      <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
        <h2 class="h3" style="margin-bottom:16px">Write to the House</h2>
        @include('partials.form', ['kind' => 'contact', 'cta' => 'Send message', 'upload' => true, 'fields' => [
          ['name', 'Name', 'text', null, true],
          ['email', 'Email', 'email', null, true],
          ['phone', 'Phone', 'tel', null, false],
          ['country', 'Country', 'text', null, false],
          ['type', 'Enquiry Type', 'select', ['General', 'Bespoke Commission', 'Wedding Footwear', 'Designer & Styling', 'Aftercare & Repair', 'International Client'], true],
          ['message', 'Message', 'textarea', null, true]
        ]])
      </div>
    </div>
  </div>
</div>
@endsection
