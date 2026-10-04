@extends('layouts.app')

@section('content')
@php
    $phone = setting('phone');
    $email = setting('email_public');
@endphp

<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Contact', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', ['title' => "Let's make something."])
  </div>
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <p>{{ setting('address_store') }}</p>
      @if ($phone)
        <p><a href="tel:{{ preg_replace('/\s/', '', $phone) }}">{{ $phone }}</a></p>
      @endif
      @if ($email)
        <p><a href="mailto:{{ $email }}">{{ $email }}</a></p>
      @endif
      <div class="row" style="margin-top:16px">
        @include('partials.wa-button', ['label' => 'Message us', 'text' => 'Hello Tōramally,'])
        <a class="btn ghost" href="{{ setting('instagram') }}" target="_blank" rel="noopener">Instagram</a>
      </div>
    </div>
    <div>
      @include('partials.form', ['kind' => 'contact', 'cta' => 'Send message', 'upload' => true, 'fields' => [
        ['name', 'Name', 'text', null, true],
        ['email', 'Email', 'email', null, true],
        ['phone', 'Phone', 'tel', null, false],
        ['country', 'Country', 'text', null, false],
        ['type', 'Enquiry', 'select', ['General', 'Bespoke', 'Wedding', 'Designer', 'International'], true],
        ['message', 'Message', 'textarea', null, true]
      ]])
    </div>
  </div>
</div>
@endsection
