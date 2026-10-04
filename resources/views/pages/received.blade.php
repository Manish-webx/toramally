@extends('layouts.app')

@section('content')
@php
  $orderNo = request()->query('order');
@endphp
<div class="wrap" style="padding:96px 20px;max-width:720px">
  <p class="label brass">The House of Tōramally</p>
  <h1 class="h1" style="margin-top:8px">Order Request Received.</h1>
  @if($orderNo)
    <div style="margin:20px 0;padding:16px 20px;background:var(--ivory-50,#f8f6f0);border:1px solid var(--line);border-radius:4px">
      <span class="label">Reference Number:</span>
      <span style="font-family:var(--serif);font-size:22px;font-weight:500;margin-left:8px;color:var(--green-900,#1F3D2B)">{{ $orderNo }}</span>
    </div>
  @endif
  <p style="margin-top:16px;line-height:1.7">Thank you. Your customer account and order details have been registered with the House. A member of the House will review your order, verify your measurements and delivery destination, and send a bespoke payment invoice within one working day.</p>
  <div class="row" style="margin-top:32px;gap:16px;flex-wrap:wrap">
    @if(auth()->check())
      <a class="btn" href="{{ route('account') }}">View in My Account</a>
    @endif
    <a class="btn {{ auth()->check() ? 'ghost' : '' }}" href="{{ url('care') }}">Caring for your pair</a>
    @include('partials.wa-button', ['label' => 'Message us on WhatsApp', 'text' => 'Hello Tōramally, I have just placed order request' . ($orderNo ? ' #' . $orderNo : '') . '.'])
  </div>
</div>
@endsection
