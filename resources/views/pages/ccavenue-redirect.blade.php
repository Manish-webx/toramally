@extends('layouts.app')

@section('content')
<div class="wrap" style="padding:100px 20px;max-width:640px;text-align:center;min-height:60vh;display:flex;flex-direction:column;justify-content:center;align-items:center">
    <div style="width:48px;height:48px;border:3px solid var(--line,#e2ded5);border-top-color:var(--green-900,#1F3D2B);border-radius:50%;animation:tmSpin 0.9s linear infinite;margin-bottom:24px"></div>
    
    <p class="label brass" style="letter-spacing:0.18em">House of Tōramally · Secure Checkout</p>
    <h1 class="h2" style="margin-top:10px;font-size:26px">Transferring to CCAvenue Gateway...</h1>
    
    <p class="muted small" style="margin-top:12px;line-height:1.6;max-width:480px">
        Please do not refresh or close this window. You are being redirected to CCAvenue's encrypted payment portal to complete payment for Order <strong>#{{ $order->order_no }}</strong> (₹{{ number_format($order->total_inr) }}).
    </p>

    <!-- Hidden Auto-Submitting Form to CCAvenue -->
    <form id="ccavenueForm" method="POST" action="{{ $action_url }}" style="margin-top:24px">
        <input type="hidden" name="encRequest" value="{{ $encRequest }}">
        <input type="hidden" name="access_code" value="{{ $access_code }}">
        
        <noscript>
            <p style="margin-bottom:12px">JavaScript appears to be disabled. Please click below to proceed:</p>
            <button type="submit" class="btn">Proceed to CCAvenue Payment</button>
        </noscript>
    </form>

    <div style="margin-top:28px">
        <button type="button" onclick="document.getElementById('ccavenueForm').submit()" class="btn ghost small" style="font-size:12px">
            Click here if you are not automatically redirected &rarr;
        </button>
    </div>
</div>

<style>
@keyframes tmSpin {
    to { transform: rotate(360deg); }
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        // Auto submit the form
        setTimeout(function() {
            var f = document.getElementById("ccavenueForm");
            if (f) {
                f.submit();
            }
        }, 300);
    });
</script>
@endsection
