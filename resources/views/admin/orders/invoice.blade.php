<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_no }} | Tōramally</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,500;0,600;1,400&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --green-900: #1F3D2B;
            --brass-500: #9C7A3C;
            --charcoal: #1E1E1E;
            --muted: #6B6862;
            --line: #E5E0D8;
            --serif: 'Cormorant Garamond', Georgia, serif;
            --sans: 'Jost', system-ui, sans-serif;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: var(--sans);
            color: var(--charcoal);
            background: #fff;
            padding: 40px;
            font-size: 14px;
            line-height: 1.5;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            border: 1px solid var(--line);
            padding: 48px;
            background: #fff;
        }
        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            border-bottom: 2px solid var(--green-900);
            padding-bottom: 24px;
            margin-bottom: 32px;
        }
        .brand-title {
            font-family: var(--serif);
            font-size: 38px;
            font-weight: 500;
            color: var(--green-900);
            letter-spacing: .08em;
            line-height: 1;
        }
        .brand-subtitle {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .15em;
            color: var(--brass-500);
            margin-top: 4px;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta h2 {
            font-family: var(--serif);
            font-size: 24px;
            color: var(--green-900);
            margin-bottom: 4px;
        }
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
            margin-bottom: 32px;
        }
        .section-label {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .12em;
            color: var(--brass-500);
            font-weight: 600;
            margin-bottom: 6px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        th {
            background: #FAF8F5;
            padding: 10px 12px;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .1em;
            text-align: left;
            border-bottom: 1px solid var(--line);
            color: var(--muted);
        }
        td {
            padding: 14px 12px;
            border-bottom: 1px solid var(--line);
            vertical-align: top;
        }
        .totals-section {
            display: flex;
            justify-content: flex-end;
            margin-top: 20px;
        }
        .totals-box {
            width: 300px;
        }
        .totals-row {
            display: flex;
            justify-content: space-between;
            padding: 4px 0;
        }
        .totals-row.grand-total {
            border-top: 2px solid var(--green-900);
            padding-top: 10px;
            margin-top: 6px;
            font-size: 18px;
            font-family: var(--serif);
            font-weight: 600;
            color: var(--green-900);
        }
        .seal-note {
            margin-top: 48px;
            padding-top: 24px;
            border-top: 1px dashed var(--line);
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 12px;
            color: var(--muted);
        }
        @media print {
            body { padding: 0; background: #fff; }
            .invoice-container { border: none; padding: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="max-width:800px;margin:0 auto 20px;display:flex;justify-content:space-between">
        <button onclick="window.print()" style="padding:8px 18px;background:var(--green-900);color:#fff;border:0;border-radius:4px;cursor:pointer;font-family:var(--sans)">Print / Save as PDF</button>
        <button onclick="window.close()" style="padding:8px 18px;background:#eee;border:0;border-radius:4px;cursor:pointer;font-family:var(--sans)">Close</button>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div>
                <div class="brand-title">Tōramally</div>
                <div class="brand-subtitle">Atelier · Lucknow &amp; Kolkata</div>
                <div style="font-size:12px;color:var(--muted);margin-top:6px">
                    House of Tōramally<br>
                    Kolkata Flagship &amp; Lucknow Workshop
                </div>
            </div>
            <div class="invoice-meta">
                <h2>BESPOKE INVOICE</h2>
                <div><strong>Invoice Ref:</strong> {{ $order->order_no }}</div>
                <div><strong>Date:</strong> {{ $order->created_at->format('d F Y') }}</div>
                <div><strong>Payment:</strong> {{ strtoupper($order->payment_status) }}</div>
            </div>
        </div>

        <div class="grid-2">
            <div>
                <div class="section-label">Billed &amp; Shipped To:</div>
                <div style="font-weight:600;font-size:16px;color:var(--green-900)">{{ $order->ship_name }}</div>
                <div>{{ $order->email }}</div>
                @if($order->phone) <div>{{ $order->phone }}</div> @endif
                <div style="margin-top:4px;color:var(--muted)">
                    {{ $order->ship_line1 }}<br>
                    @if($order->ship_line2) {{ $order->ship_line2 }}<br> @endif
                    {{ $order->ship_city }}{{ $order->ship_state ? ', ' . $order->ship_state : '' }} - {{ $order->ship_postcode }}<br>
                    {{ $order->ship_country ?: 'India' }}
                </div>
            </div>

            <div style="text-align:right">
                <div class="section-label">Order Details:</div>
                <div><strong>Making Status:</strong> {{ $order->status }}</div>
                <div><strong>Currency:</strong> {{ $order->currency ?: 'INR' }}</div>
                @if($order->is_international)
                    <div style="color:var(--brass-500);font-weight:500">Insured International Courier</div>
                @else
                    <div>Insured Domestic Courier</div>
                @endif
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th>Item Description</th>
                    <th>Specifications / Craft</th>
                    <th>Qty</th>
                    <th>Unit (INR)</th>
                    <th style="text-align:right">Total (INR)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            <div style="font-weight:600;font-size:15px;color:var(--green-900)">{{ $item->name }}</div>
                            @if($item->hsn_code)
                                <div style="font-size:11px;color:var(--muted)">HSN: {{ $item->hsn_code }}</div>
                            @endif
                        </td>
                        <td>
                            <div>{{ $item->colour ?: 'Natural' }} · {{ $item->size ?: 'Standard' }}</div>
                            @if($item->personalisation_json)
                                @php $p = json_decode($item->personalisation_json, true) ?: []; @endphp
                                <div style="font-size:11px;color:var(--brass-500)">
                                    @if(!empty($p['initials'])) Initials: {{ $p['initials'] }} @endif
                                    @if(!empty($p['gold'])) (Gold) @endif
                                </div>
                            @endif
                        </td>
                        <td>{{ $item->qty }}</td>
                        <td>₹{{ number_format($item->unit_price_inr) }}</td>
                        <td style="text-align:right">₹{{ number_format($item->unit_price_inr * $item->qty) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals-section">
            <div class="totals-box">
                <div class="totals-row">
                    <span style="color:var(--muted)">Subtotal:</span>
                    <span>₹{{ number_format($order->subtotal_inr) }}</span>
                </div>
                @if($order->patron_benefit_inr > 0)
                    <div class="totals-row" style="color:var(--green-900)">
                        <span>Patron Benefit:</span>
                        <span>- ₹{{ number_format($order->patron_benefit_inr) }}</span>
                    </div>
                @endif
                <div class="totals-row">
                    <span style="color:var(--muted)">Insured Shipping:</span>
                    <span>{{ $order->shipping_inr > 0 ? '₹' . number_format($order->shipping_inr) : 'Complimentary' }}</span>
                </div>
                <div class="totals-row grand-total">
                    <span>Total Amount:</span>
                    <span>₹{{ number_format($order->total_inr) }}</span>
                </div>
            </div>
        </div>

        <div class="seal-note">
            <div>
                <em>Crafted in silence by the artisans of Tōramally.</em>
            </div>
            <div style="text-align:right">
                Authorized Atelier Signature<br>
                <strong>House of Tōramally</strong>
            </div>
        </div>
    </div>
</body>
</html>
