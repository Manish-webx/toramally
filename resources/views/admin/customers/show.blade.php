@extends('admin.layout', ['title' => 'Patron: ' . $customer->full_name, 'header' => 'Client Profile: ' . $customer->full_name])

@section('content')
<div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
    <a href="{{ route('admin.customers.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Patrons</a>
    @if($customer->phone)
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}?text={{ urlencode('Hello ' . $customer->first_name . ', from House of Tōramally:') }}" target="_blank" class="btn-atelier btn-atelier-brass btn-atelier-sm">
            Message on WhatsApp
        </a>
    @endif
</div>

<div class="row g-4">
    <!-- Left Column: Customer Profile & Order History -->
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="card-title">Order History with Atelier</div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->orders as $ord)
                            <tr>
                                <td>
                                    <strong><a href="{{ route('admin.orders.show', $ord->id) }}" style="color:var(--green-900);text-decoration:none">{{ $ord->order_no }}</a></strong>
                                </td>
                                <td>
                                    @foreach($ord->items as $itm)
                                        <div>{{ $itm->name }} ({{ $itm->colour }}, {{ $itm->size }}) × {{ $itm->qty }}</div>
                                    @endforeach
                                </td>
                                <td><strong>₹{{ number_format($ord->total_inr) }}</strong></td>
                                <td>
                                    <span class="pill {{ strtolower($ord->payment_status)==='paid' ? 'pill-paid' : 'pill-unpaid' }}">
                                        {{ ucfirst($ord->payment_status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="pill pill-placed">{{ $ord->status }}</span>
                                </td>
                                <td style="font-size:12px;color:var(--muted)">{{ $ord->created_at->format('d M Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $ord->id) }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">Inspect</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:24px;color:var(--muted)">
                                    No orders placed by this customer yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($customer->sizes->count() > 0)
            <div class="card-custom">
                <div class="card-title">Recorded Size Measurements</div>
                <div class="row g-2">
                    @foreach($customer->sizes as $sz)
                        <div class="col-6 col-md-3">
                            <div style="background:var(--ivory-50);padding:8px 12px;border:1px solid var(--line);border-radius:4px">
                                <div style="font-size:11px;color:var(--muted)">{{ $sz->silhouette ?: 'Footwear' }}</div>
                                <div style="font-weight:600">{{ $sz->size }}</div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Right Column: Profile & Addresses -->
    <div class="col-12 col-lg-4">
        <div class="card-custom">
            <div class="card-title">Patron Information</div>

            <div style="margin-bottom:12px">
                <div class="stat-label">Full Name</div>
                <div style="font-weight:600;font-size:16px;color:var(--green-900)">{{ $customer->full_name }}</div>
            </div>

            <div style="margin-bottom:12px">
                <div class="stat-label">Email Address</div>
                <div>{{ $customer->email }}</div>
            </div>

            <div style="margin-bottom:12px">
                <div class="stat-label">Phone / WhatsApp</div>
                <div>{{ $customer->phone ?: 'Not provided' }}</div>
            </div>

            <div style="margin-bottom:12px">
                <div class="stat-label">Lifetime Spend</div>
                <div style="font-weight:600;font-size:18px;color:var(--brass-500)">
                    ₹{{ number_format($customer->orders->whereNotIn('status', ['Cancelled'])->sum('total_inr')) }}
                </div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-title">Saved Delivery Addresses</div>

            @forelse($customer->addresses as $addr)
                <div style="background:var(--ivory-50);padding:12px;border-radius:4px;border:1px solid var(--line);margin-bottom:10px;font-size:13px">
                    @if($addr->is_default)
                        <span class="pill" style="background:var(--brass-100);color:var(--brass-600);font-size:10px;margin-bottom:4px;display:inline-block">Default Address</span>
                    @endif
                    <div>{{ $addr->line1 }}</div>
                    @if($addr->line2) <div>{{ $addr->line2 }}</div> @endif
                    <div>{{ $addr->city }}{{ $addr->state ? ', ' . $addr->state : '' }} - {{ $addr->postcode }}</div>
                    <div><strong>{{ $addr->country ?: 'India' }}</strong></div>
                </div>
            @empty
                <div style="color:var(--muted);font-size:13px">No saved addresses.</div>
            @endforelse
        </div>
    </div>
</div>
@endsection
