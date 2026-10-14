@extends('layouts.app')

@section('content')
<div class="wrap" style="padding:96px 20px;max-width:680px">
    <p class="label" style="color:var(--rose-800,#8b263e)">Payment Incomplete</p>
    <h1 class="h1" style="margin-top:8px">{{ $errorTitle ?? 'Payment Unsuccessful' }}</h1>
    
    @if(isset($order) && $order)
        <div style="margin:20px 0;padding:16px 20px;background:var(--ivory-50,#f8f6f0);border:1px solid var(--line);border-radius:4px">
            <div class="row" style="justify-content:space-between;align-items:center">
                <div>
                    <span class="label">Order Reference:</span>
                    <span style="font-family:var(--serif);font-size:20px;font-weight:500;margin-left:8px;color:var(--green-900,#1F3D2B)">{{ $order->order_no }}</span>
                </div>
                <div style="font-size:18px;font-weight:600">₹{{ number_format($order->total_inr) }}</div>
            </div>
            @if(isset($trackingId) && $trackingId)
                <div class="small muted" style="margin-top:6px">CCAvenue Reference: {{ $trackingId }}</div>
            @endif
        </div>
    @endif

    <p style="margin-top:16px;line-height:1.7;color:var(--ink-700)">
        {{ $errorMessage ?? 'We were unable to verify payment for this transaction. If amount was debited from your bank account, it will be automatically reversed or confirmed by CCAvenue within 24-48 hours.' }}
    </p>

    <div class="row" style="margin-top:32px;gap:14px;flex-wrap:wrap">
        @if(isset($order) && $order && $order->payment_status !== 'Paid')
            <a class="btn" href="{{ route('ccavenue.pay', ['orderNo' => $order->order_no]) }}">
                Retry Payment via CCAvenue &rarr;
            </a>
        @endif
        <a class="btn ghost" href="{{ route('shop') }}">Continue Browsing</a>
        @include('partials.wa-button', ['label' => 'Concierge Assistance', 'text' => 'Hello Tōramally, I encountered an issue paying for order ' . (isset($order) && $order ? '#' . $order->order_no : '') . '. Please assist me.'])
    </div>
</div>
@endsection
