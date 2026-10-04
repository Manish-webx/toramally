@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Restoration', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => 'Give them another life.', 'sub' => 'Sole and heel replacement, re-patina, restoration, conditioning, artwork restoration and hardware replacement.'])
  </div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <p>Send us photographs of the pair, from the side, the sole and any damage. We reply with what can be done, the cost and the time. Welted Tōramally pairs can be resoled in the workshop.</p>
      @include('partials.wa-button', ['label' => 'Request restoration', 'text' => 'Hello Tōramally, I would like to restore a pair. I will send photographs of the side, sole and any damage.'])
    </div>
    <div>
      @include('partials.form', ['kind' => 'restoration', 'cta' => 'Request restoration', 'upload' => true, 'fields' => [
        ['name', 'Name', 'text', null, true],
        ['contact', 'Email or WhatsApp number', 'text', null, true],
        ['service', 'Service', 'select', ['Sole and heel replacement', 'Re-patina', 'Full restoration', 'Conditioning', 'Artwork restoration', 'Hardware replacement', 'Not sure'], true],
        ['notes', 'About the pair', 'textarea', null, false]
      ]])
    </div>
  </div>
</div>
@endsection
