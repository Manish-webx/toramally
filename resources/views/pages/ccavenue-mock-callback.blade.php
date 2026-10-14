@extends('layouts.app')

@section('content')
<div class="wrap" style="padding:100px 20px;max-width:640px;text-align:center;min-height:60vh;display:flex;flex-direction:column;justify-content:center;align-items:center">
    <div style="width:48px;height:48px;border:3px solid var(--line,#e2ded5);border-top-color:var(--green-900,#1F3D2B);border-radius:50%;animation:tmSpin 0.9s linear infinite;margin-bottom:24px"></div>
    
    <p class="label brass" style="letter-spacing:0.18em">House of Tōramally · Processing</p>
    <h1 class="h2" style="margin-top:10px;font-size:26px">Verifying Payment Response...</h1>
    
    <p class="muted small" style="margin-top:12px;line-height:1.6;max-width:480px">
        Please wait while we securely decrypt and verify your payment confirmation with our servers.
    </p>

    <!-- Hidden Auto-Submitting Callback Form -->
    <form id="callbackForm" method="POST" action="{{ $redirect_url }}" style="margin-top:24px">
        @csrf
        <input type="hidden" name="encResp" value="{{ $encResp }}">
        <noscript>
            <button type="submit" class="btn">Proceed to Confirmation &rarr;</button>
        </noscript>
    </form>
</div>

<style>
@keyframes tmSpin {
    to { transform: rotate(360deg); }
}
</style>

<script>
    document.addEventListener("DOMContentLoaded", function() {
        setTimeout(function() {
            var f = document.getElementById("callbackForm");
            if (f) {
                f.submit();
            }
        }, 300);
    });
</script>
@endsection
