@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Aftercare & Repairs', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'Aftercare & Repairs', 
      'sub' => 'Our relationship with a pair does not end at delivery. Every Tōramally can come home to the atelier for care.'
    ])
  </div>

  <div class="grid g2" style="gap:32px;margin:32px 0 48px">
    <div class="tile" style="padding:24px">
      <h3 class="h3">The Shoe Shine Service</h3>
      <p style="margin-top:8px">Cleaning, conditioning and a full mirror shine by our finishers.</p>
      <div style="margin-top:12px"><a class="tlink" href="{{ url('shoe-shine-service') }}">Book Shoe Shine</a></div>
    </div>

    <div class="tile" style="padding:24px">
      <h3 class="h3">Re-patina and Colour Refresh</h3>
      <p style="margin-top:8px">Restoring or deepening the patina, or a new colour on request. Price on assessment.</p>
    </div>

    <div class="tile" style="padding:24px">
      <h3 class="h3">Paint and Artwork Touch-up</h3>
      <p style="margin-top:8px">Restoring hand-painted, miniature and tattooed work by the artists who made it.</p>
    </div>

    <div class="tile" style="padding:24px">
      <h3 class="h3">Resoling and Heel Replacement</h3>
      <p style="margin-top:8px">Hand-welted and hand-stitched Blake pairs can be resoled on their original last. Cemented pairs are assessed case by case. Price on assessment.</p>
    </div>

    <div class="tile" style="padding:24px;grid-column:1/-1">
      <h3 class="h3">Repairs</h3>
      <p style="margin-top:8px">Stitching, linings, hardware, scuffs and tears.</p>
    </div>
  </div>

  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <h2 class="h3">How to send a pair</h2>
      <div style="margin-top:16px;line-height:1.7">
        <p>Request aftercare in your account or write to <a href="mailto:care@toramally.com">care@toramally.com</a> with photographs. We assess and quote before any work.</p>
        <p>Pairs can also be dropped at our Kolkata flagship. Typical turnaround is 2–4 weeks plus shipping. Repairs for faults in our making are free.</p>
      </div>
      <div style="margin-top:24px">
        @include('partials.wa-button', ['label' => 'Request aftercare on WhatsApp', 'text' => 'Hello Tōramally, I would like to request aftercare for a pair. I will send photographs of the pair and details of the work needed.'])
      </div>
    </div>
    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3" style="margin-bottom:16px">Send an Aftercare Request</h3>
      @include('partials.form', ['kind' => 'restoration', 'cta' => 'Submit request', 'upload' => true, 'fields' => [
        ['name', 'Name', 'text', null, true],
        ['contact', 'Email or WhatsApp number', 'text', null, true],
        ['service', 'Service Required', 'select', [
          'The Shoe Shine Service',
          'Re-patina and Colour Refresh',
          'Paint and Artwork Touch-up',
          'Resoling and Heel Replacement',
          'Repairs (stitching, linings, hardware, scuffs)',
          'Multiple / Not sure'
        ], true],
        ['notes', 'About the pair and damage/request', 'textarea', null, false]
      ]])
    </div>
  </div>
</div>
@endsection
