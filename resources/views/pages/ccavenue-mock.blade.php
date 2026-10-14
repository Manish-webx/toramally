@extends('layouts.app')

@section('content')
<div class="wrap" style="padding:60px 20px;max-width:760px;margin:0 auto">
    <!-- Header Banner -->
    <div style="background:var(--green-900,#1F3D2B);color:#fff;border-radius:12px 12px 0 0;padding:24px 30px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:16px">
        <div>
            <div style="font-size:11px;letter-spacing:0.2em;text-transform:uppercase;color:var(--brass-300,#d9be75);margin-bottom:4px">House of Tōramally · Test Sandbox</div>
            <h1 style="font-family:'Cormorant Garamond',serif;font-size:26px;margin:0;font-weight:600;letter-spacing:0.02em">CCAvenue Payment Gateway Simulator</h1>
        </div>
        <div style="text-align:right">
            <span style="display:inline-block;padding:4px 10px;background:rgba(217,190,117,0.2);border:1px solid rgba(217,190,117,0.4);border-radius:20px;font-size:11px;letter-spacing:0.08em;color:var(--brass-300,#d9be75);text-transform:uppercase;font-weight:600">Local Test Mode</span>
        </div>
    </div>

    <!-- Main Container -->
    <div style="background:#fff;border:1px solid var(--line,#e2ded5);border-top:none;border-radius:0 0 12px 12px;padding:32px;box-shadow:0 12px 40px rgba(0,0,0,0.06)">
        
        <!-- Order Summary Card -->
        <div style="background:var(--ivory-50,#faf8f5);border:1px solid var(--line,#e2ded5);border-radius:8px;padding:20px;margin-bottom:28px;display:flex;justify-content:space-between;align-items:center;flex-wrap:wrap;gap:16px">
            <div>
                <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500,#736d62)">Order Number</div>
                <div style="font-size:18px;font-weight:600;color:var(--ink-900,#171614);margin-top:2px">#{{ $orderNo }}</div>
                @if(!empty($params['billing_name']))
                    <div style="font-size:13px;color:var(--ink-600,#5c574e);margin-top:2px">{{ $params['billing_name'] }} · {{ $params['billing_email'] ?? '' }}</div>
                @endif
            </div>
            <div style="text-align:right">
                <div style="font-size:12px;text-transform:uppercase;letter-spacing:0.1em;color:var(--ink-500,#736d62)">Amount to Pay</div>
                <div style="font-family:'Cormorant Garamond',serif;font-size:30px;font-weight:700;color:var(--green-900,#1F3D2B);margin-top:2px">₹{{ number_format((float)$amount) }}</div>
            </div>
        </div>

        <form id="mockPaymentForm" method="POST" action="{{ route('ccavenue.mock.process') }}">
            @csrf
            <input type="hidden" name="order_id" value="{{ $orderNo }}">
            <input type="hidden" name="amount" value="{{ $amount }}">
            <input type="hidden" id="formAction" name="action" value="success">

            <!-- Payment Methods -->
            <div style="margin-bottom:28px">
                <label style="font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:0.08em;display:block;margin-bottom:12px;color:var(--ink-800,#2b2823)">
                    Select Simulated Payment Method
                </label>

                <div style="display:grid;grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));gap:12px;margin-bottom:20px">
                    <label style="display:flex;align-items:center;gap:10px;padding:14px 16px;border:1.5px solid var(--line,#e2ded5);border-radius:8px;cursor:pointer;background:#fff;transition:all 0.2s" class="pay-method-opt">
                        <input type="radio" name="payment_mode" value="Credit Card" checked style="accent-color:var(--green-900,#1F3D2B)">
                        <div>
                            <div style="font-weight:600;font-size:14px">Credit / Debit Card</div>
                            <div style="font-size:11.5px;color:var(--ink-500,#736d62)">Visa, Mastercard, RuPay</div>
                        </div>
                    </label>

                    <label style="display:flex;align-items:center;gap:10px;padding:14px 16px;border:1.5px solid var(--line,#e2ded5);border-radius:8px;cursor:pointer;background:#fff;transition:all 0.2s" class="pay-method-opt">
                        <input type="radio" name="payment_mode" value="UPI / QR" style="accent-color:var(--green-900,#1F3D2B)">
                        <div>
                            <div style="font-weight:600;font-size:14px">UPI &amp; QR</div>
                            <div style="font-size:11.5px;color:var(--ink-500,#736d62)">GPay, PhonePe, Paytm</div>
                        </div>
                    </label>

                    <label style="display:flex;align-items:center;gap:10px;padding:14px 16px;border:1.5px solid var(--line,#e2ded5);border-radius:8px;cursor:pointer;background:#fff;transition:all 0.2s" class="pay-method-opt">
                        <input type="radio" name="payment_mode" value="Net Banking" style="accent-color:var(--green-900,#1F3D2B)">
                        <div>
                            <div style="font-weight:600;font-size:14px">Net Banking</div>
                            <div style="font-size:11.5px;color:var(--ink-500,#736d62)">HDFC, ICICI, SBI, Axis</div>
                        </div>
                    </label>
                </div>

                <!-- Card Details Preview Box -->
                <div style="background:var(--ivory-100,#f4f1ea);border-radius:8px;padding:16px 20px;font-size:12.5px;color:var(--ink-700,#444)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:6px">
                        <span><strong>Test Card:</strong> 4111 2222 3333 4444</span>
                        <span><strong>Expiry:</strong> 12/28</span>
                        <span><strong>CVV:</strong> 123</span>
                    </div>
                    <div style="color:var(--ink-500,#736d62);font-size:11.5px">
                        ✓ Simulator automatically mimics CCAvenue 128-bit AES payload encryption &amp; callback roundtrip.
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div style="display:flex;flex-direction:column;gap:12px">
                <button type="button" onclick="submitSimulation('success')" class="btn" style="background:var(--green-900,#1F3D2B);color:#fff;padding:14px 24px;font-size:14px;border:none;cursor:pointer;border-radius:6px;font-weight:600;letter-spacing:0.05em;display:flex;align-items:center;justify-content:center;gap:8px">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                    Simulate Successful Payment (₹{{ number_format((float)$amount) }})
                </button>

                <div style="display:grid;grid-template-columns:1fr 1fr;gap:12px">
                    <button type="button" onclick="submitSimulation('failure')" class="btn ghost" style="padding:11px 16px;font-size:13px;color:#c0392b;border:1px solid rgba(192,57,43,0.3);background:#fff;cursor:pointer;border-radius:6px;font-weight:500">
                        ✕ Simulate Bank Decline / Failure
                    </button>

                    <button type="button" onclick="submitSimulation('cancel')" class="btn ghost" style="padding:11px 16px;font-size:13px;color:var(--ink-600,#666);border:1px solid var(--line,#e2ded5);background:#fff;cursor:pointer;border-radius:6px;font-weight:500">
                        ⊘ Cancel &amp; Abort Session
                    </button>
                </div>
            </div>
        </form>

        <div style="margin-top:28px;padding-top:20px;border-top:1px solid var(--line,#e2ded5);text-align:center;font-size:11.5px;color:var(--ink-500,#736d62)">
            When switching to production, simply change <code>CCAVENUE_MODE=live</code> in your <code>.env</code> file with live merchant keys.
        </div>
    </div>
</div>

<script>
function submitSimulation(action) {
    document.getElementById('formAction').value = action;
    document.getElementById('mockPaymentForm').submit();
}
</script>
@endsection
