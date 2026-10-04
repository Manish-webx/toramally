@extends('admin.layout', ['title' => 'Order #' . $order->order_no, 'header' => 'Order Details: #' . $order->order_no])

@section('content')
<div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
    <a href="{{ route('admin.orders.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Orders</a>
    <div style="display:flex;gap:8px">
        <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn-atelier btn-atelier-secondary btn-atelier-sm">
            🖨 Print Invoice / Work Order
        </a>
        @if($order->phone)
            <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->phone) }}?text={{ urlencode('Hello ' . $order->ship_name . ', regarding your Tōramally order #' . $order->order_no . ':') }}" target="_blank" class="btn-atelier btn-atelier-brass btn-atelier-sm">
                Message Client on WhatsApp
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Order Items & Pricing -->
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="card-title">
                <span>Handcrafted Items in this Order</span>
                <span class="pill" style="background:var(--ivory-100)">{{ $order->items->sum('qty') }} total</span>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Item / Silhouette</th>
                            <th>Details</th>
                            <th>Qty</th>
                            <th>Unit Price</th>
                            <th>Total</th>
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
                                    <div><strong>Colour:</strong> {{ $item->colour ?: 'Standard' }}</div>
                                    <div><strong>Size:</strong> {{ $item->size ?: 'Standard / Custom' }}</div>
                                    @if($item->personalisation_json)
                                        @php $p = json_decode($item->personalisation_json, true) ?: []; @endphp
                                        <div style="font-size:12px;color:var(--brass-500);margin-top:4px">
                                            @if(!empty($p['initials'])) Initials: {{ $p['initials'] }} @endif
                                            @if(!empty($p['gold'])) (Gold) @endif
                                            @if(!empty($p['nails'])) · Brass Nails Sole @endif
                                        </div>
                                    @endif
                                </td>
                                <td>{{ $item->qty }}</td>
                                <td>₹{{ number_format($item->unit_price_inr) }}</td>
                                <td><strong>₹{{ number_format($item->unit_price_inr * $item->qty) }}</strong></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Price Breakdown -->
            <div style="margin-top:20px;padding-top:16px;border-top:1px solid var(--line);display:flex;justify-content:flex-end">
                <div style="width:280px">
                    <div style="display:flex;justify-content:space-between;padding:4px 0">
                        <span style="color:var(--muted)">Subtotal:</span>
                        <span>₹{{ number_format($order->subtotal_inr) }}</span>
                    </div>
                    @if($order->patron_benefit_inr > 0)
                        <div style="display:flex;justify-content:space-between;padding:4px 0;color:var(--green-700)">
                            <span>Patron Benefit:</span>
                            <span>- ₹{{ number_format($order->patron_benefit_inr) }}</span>
                        </div>
                    @endif
                    @if($order->code_benefit_inr > 0)
                        <div style="display:flex;justify-content:space-between;padding:4px 0;color:var(--green-700)">
                            <span>Promo Discount:</span>
                            <span>- ₹{{ number_format($order->code_benefit_inr) }}</span>
                        </div>
                    @endif
                    <div style="display:flex;justify-content:space-between;padding:4px 0">
                        <span style="color:var(--muted)">Insured Shipping:</span>
                        <span>{{ $order->shipping_inr > 0 ? '₹' . number_format($order->shipping_inr) : 'Complimentary' }}</span>
                    </div>
                    <div style="display:flex;justify-content:space-between;padding:10px 0 0;margin-top:6px;border-top:1px solid var(--line);font-size:16px;font-weight:600;color:var(--green-900)">
                        <span>Total Payable:</span>
                        <span>₹{{ number_format($order->total_inr) }}</span>
                    </div>
                    @if($order->currency && $order->currency !== 'INR')
                        <div style="display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-top:2px">
                            <span>FX Charged:</span>
                            <span>{{ $order->currency }} {{ number_format($order->total_charged) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            @if($order->gift_note)
                <div style="margin-top:20px;background:var(--ivory-100);padding:14px;border-radius:6px;border:1px dashed var(--brass-500)">
                    <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:var(--brass-500);margin-bottom:4px">Client Gift Note:</div>
                    <div style="font-style:italic">“{{ $order->gift_note }}”</div>
                </div>
            @endif
        </div>

        <!-- Status Update & History -->
        <div class="card-custom">
            <div class="card-title">Order Lifecycle &amp; Status History</div>

            <form action="{{ route('admin.orders.status', $order->id) }}" method="POST" style="background:var(--ivory-50);padding:16px;border-radius:6px;border:1px solid var(--line);margin-bottom:20px">
                @csrf
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label-custom">Update Lifecycle Status</label>
                        <select name="status" class="form-select-custom">
                            <option value="Order Placed" {{ $order->status==='Order Placed'?'selected':'' }}>Order Placed (Received)</option>
                            <option value="Confirmed" {{ $order->status==='Confirmed'?'selected':'' }}>Confirmed (Atelier Accepted)</option>
                            <option value="In Crafting" {{ $order->status==='In Crafting'?'selected':'' }}>In Crafting (Workshop Lasting/Art)</option>
                            <option value="Quality Check" {{ $order->status==='Quality Check'?'selected':'' }}>Quality Check (Mirror Polish/Glaze)</option>
                            <option value="Shipped" {{ $order->status==='Shipped'?'selected':'' }}>Shipped (Dispatched)</option>
                            <option value="Delivered" {{ $order->status==='Delivered'?'selected':'' }}>Delivered</option>
                            <option value="Cancelled" {{ $order->status==='Cancelled'?'selected':'' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label-custom">Status Note / Milestone</label>
                        <input type="text" name="notes" class="form-control-custom" placeholder="e.g. Leather lasted, miniature painting in progress">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn-atelier btn-atelier-primary full" style="width:100%;justify-content:center">Update Status</button>
                    </div>
                </div>
            </form>

            <div class="timeline" style="border-left:2px solid var(--line);padding-left:16px;margin-left:10px">
                @forelse($order->statusHistory as $hist)
                    <div style="margin-bottom:16px;position:relative">
                        <div style="position:absolute;left:-23px;top:2px;width:12px;height:12px;border-radius:50%;background:var(--brass-500);border:2px solid #fff"></div>
                        <div style="display:flex;justify-content:space-between">
                            <strong>{{ $hist->status }}</strong>
                            <span style="font-size:11.5px;color:var(--muted)">{{ $hist->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <div style="font-size:12.5px;color:var(--muted)">{{ $hist->note }}</div>
                    </div>
                @empty
                    <div style="font-size:12.5px;color:var(--muted)">No previous status updates recorded.</div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Customer & Delivery Info -->
    <div class="col-12 col-lg-4">
        <!-- Customer & Shipping -->
        <div class="card-custom">
            <div class="card-title">Client &amp; Delivery Destination</div>

            <div style="margin-bottom:16px">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Recipient Name</div>
                <div style="font-weight:600;font-size:15px;color:var(--green-900)">{{ $order->ship_name }}</div>
            </div>

            <div style="margin-bottom:16px">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Contact Channels</div>
                <div>✉ {{ $order->email }}</div>
                @if($order->phone)
                    <div style="margin-top:2px">✆ {{ $order->phone }}</div>
                @endif
            </div>

            <div style="margin-bottom:16px">
                <div style="font-size:11px;text-transform:uppercase;letter-spacing:.08em;color:var(--muted)">Shipping Address</div>
                <div style="background:var(--ivory-50);padding:10px;border-radius:4px;border:1px solid var(--line);font-size:13px">
                    {{ $order->ship_line1 }}<br>
                    @if($order->ship_line2) {{ $order->ship_line2 }}<br> @endif
                    {{ $order->ship_city }}{{ $order->ship_state ? ', ' . $order->ship_state : '' }} - {{ $order->ship_postcode }}<br>
                    <strong>{{ $order->ship_country ?: 'India' }}</strong>
                </div>
            </div>

            @if($order->customer)
                <div style="padding-top:12px;border-top:1px solid var(--line)">
                    <a href="{{ route('admin.customers.show', $order->customer->id) }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm" style="width:100%;justify-content:center">
                        View Patron Profile
                    </a>
                </div>
            @endif
        </div>

        <!-- Payment Status Control -->
        <div class="card-custom">
            <div class="card-title">Payment Controls</div>

            <form action="{{ route('admin.orders.payment', $order->id) }}" method="POST">
                @csrf
                <div style="margin-bottom:12px">
                    <label class="form-label-custom">Payment Settlement</label>
                    <select name="payment_status" class="form-select-custom">
                        <option value="pending" {{ strtolower($order->payment_status)==='pending'?'selected':'' }}>Pending / Unpaid</option>
                        <option value="paid" {{ strtolower($order->payment_status)==='paid'?'selected':'' }}>Paid &amp; Verified</option>
                        <option value="refunded" {{ strtolower($order->payment_status)==='refunded'?'selected':'' }}>Refunded</option>
                        <option value="failed" {{ strtolower($order->payment_status)==='failed'?'selected':'' }}>Failed / Declined</option>
                    </select>
                </div>
                <button type="submit" class="btn-atelier btn-atelier-primary full" style="width:100%;justify-content:center">Save Payment State</button>
            </form>
        </div>

        <!-- Internal Workshop Notes -->
        <div class="card-custom">
            <div class="card-title">Atelier Internal Notes</div>

            <form action="{{ route('admin.orders.notes', $order->id) }}" method="POST">
                @csrf
                <div style="margin-bottom:12px">
                    <textarea name="admin_notes" rows="4" class="form-control-custom" placeholder="Internal production notes, leather batch, artisan assigned...">{{ $order->admin_notes }}</textarea>
                </div>
                <button type="submit" class="btn-atelier btn-atelier-secondary full" style="width:100%;justify-content:center">Save Notes</button>
            </form>
        </div>
    </div>
</div>
@endsection
