@extends('admin.layout', ['title' => 'Dashboard Overview', 'header' => 'Atelier Overview'])

@section('content')
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <span class="stat-label">Total Revenue</span>
            <span class="stat-value">₹{{ number_format($totalRevenue) }}</span>
            <span class="stat-sub">Across all orders</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <span class="stat-label">Total Orders</span>
            <span class="stat-value">{{ $totalOrders }}</span>
            <span class="stat-sub"><a href="{{ route('admin.orders.index') }}" style="color:var(--brass-500);text-decoration:none">View all →</a></span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card green">
            <span class="stat-label">Pending Orders</span>
            <span class="stat-value">{{ $pendingOrders }}</span>
            <span class="stat-sub">Requires confirmation</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <span class="stat-label">In Crafting</span>
            <span class="stat-value">{{ $inCraftingOrders }}</span>
            <span class="stat-sub">Active in workshop</span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <span class="stat-label">Low Stock Variants</span>
            <span class="stat-value" style="{{ $lowStockCount > 0 ? 'color:#C5221F' : '' }}">{{ $lowStockCount }}</span>
            <span class="stat-sub"><a href="{{ route('admin.inventory.index') }}" style="color:var(--brass-500);text-decoration:none">Manage stock →</a></span>
        </div>
    </div>
    <div class="col-6 col-md-4 col-xl-2">
        <div class="stat-card">
            <span class="stat-label">Registered Patrons</span>
            <span class="stat-value">{{ $totalCustomers }}</span>
            <span class="stat-sub"><a href="{{ route('admin.customers.index') }}" style="color:var(--brass-500);text-decoration:none">Clients roster →</a></span>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders Section -->
    <div class="col-12 col-xl-8">
        <div class="card-custom">
            <div class="card-title">
                <span>Recent Client Orders</span>
                <a href="{{ route('admin.orders.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">View All Orders</a>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Order #</th>
                            <th>Customer</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Payment</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOrders as $o)
                            <tr>
                                <td>
                                    <strong><a href="{{ route('admin.orders.show', $o->id) }}" style="color:var(--green-900);text-decoration:none">{{ $o->order_no }}</a></strong>
                                    <div style="font-size:11px;color:var(--muted)">{{ $o->created_at->format('d M Y, h:i A') }}</div>
                                </td>
                                <td>
                                    <div style="font-weight:500">{{ $o->ship_name ?: ($o->customer ? $o->customer->full_name : 'Guest') }}</div>
                                    <div style="font-size:11.5px;color:var(--muted)">{{ $o->email }}</div>
                                </td>
                                <td>
                                    <span class="pill" style="background:var(--ivory-100)">{{ $o->items->sum('qty') }} pair(s)</span>
                                </td>
                                <td>
                                    <strong>₹{{ number_format($o->total_inr) }}</strong>
                                </td>
                                <td>
                                    @if(strtolower($o->payment_status) === 'paid')
                                        <span class="pill pill-paid">Paid</span>
                                    @else
                                        <span class="pill pill-unpaid">{{ ucfirst($o->payment_status) }}</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $s = strtolower($o->status);
                                        $class = 'pill-placed';
                                        if(str_contains($s, 'confirm')) $class = 'pill-confirmed';
                                        elseif(str_contains($s, 'craft') || str_contains($s, 'product')) $class = 'pill-crafting';
                                        elseif(str_contains($s, 'ship')) $class = 'pill-shipped';
                                        elseif(str_contains($s, 'deliver')) $class = 'pill-delivered';
                                        elseif(str_contains($s, 'cancel')) $class = 'pill-cancelled';
                                    @endphp
                                    <span class="pill {{ $class }}">{{ $o->status }}</span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $o->id) }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Inspect</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" style="text-align:center;padding:32px;color:var(--muted)">
                                    No client orders placed yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Low Stock Alert Box -->
        <div class="card-custom">
            <div class="card-title">
                <span>Inventory &amp; Low Stock Watchlist</span>
                <a href="{{ route('admin.inventory.index') }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">Inventory Manager</a>
            </div>

            <div class="table-responsive">
                <table class="custom-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Silhouette</th>
                            <th>Size</th>
                            <th>Stock Count</th>
                            <th>Availability Mode</th>
                            <th>Quick Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lowStockItems as $stk)
                            <tr>
                                <td>
                                    <strong>{{ $stk->product ? $stk->product->name : 'N/A' }}</strong>
                                </td>
                                <td>{{ $stk->product ? $stk->product->silhouette : '-' }}</td>
                                <td><span class="pill" style="background:var(--ivory-100)">{{ $stk->size }}</span></td>
                                <td>
                                    @if($stk->qty == 0)
                                        <span class="pill pill-stock-out">0 (Out of stock)</span>
                                    @else
                                        <span class="pill pill-stock-low">{{ $stk->qty }} left</span>
                                    @endif
                                </td>
                                <td>{{ $stk->product ? $stk->product->availability : '-' }}</td>
                                <td>
                                    <form action="{{ route('admin.inventory.update') }}" method="POST" style="display:inline-flex;gap:4px;align-items:center">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $stk->product_id }}">
                                        <input type="hidden" name="size" value="{{ $stk->size }}">
                                        <input type="hidden" name="qty" value="{{ $stk->qty + 1 }}">
                                        <button type="submit" class="btn-atelier btn-atelier-secondary btn-atelier-sm" title="Add 1 to stock">+1 Stock</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" style="text-align:center;padding:24px;color:var(--muted)">
                                    All inventory levels are healthy.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Right Column: Bespoke & Enquiries -->
    <div class="col-12 col-xl-4">
        <!-- Bespoke Commissions -->
        <div class="card-custom">
            <div class="card-title">
                <span>Bespoke Builder Builds</span>
                <a href="{{ route('admin.commissions.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">View All</a>
            </div>

            @forelse($recentCommissions as $cm)
                @php $b = json_decode($cm->build_json, true) ?: []; @endphp
                <div style="padding:12px 0;border-bottom:1px solid var(--line)">
                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                        <strong><a href="{{ route('admin.commissions.show', $cm->id) }}" style="color:var(--green-900);text-decoration:none">{{ $cm->ref }}</a></strong>
                        <span class="pill pill-crafting">{{ $cm->status }}</span>
                    </div>
                    <div style="font-size:13px;font-weight:500">{{ $cm->name }} · {{ $cm->contact }}</div>
                    <div style="font-size:12px;color:var(--muted);margin-top:2px">
                        {{ $b['sil'] ?? 'Silhouette' }} · {{ $b['colour'] ?? '' }} · {{ $b['craft'] ?? '' }}
                    </div>
                    <div style="font-size:12px;color:var(--brass-500);font-weight:500;margin-top:4px">
                        Est: ₹{{ number_format($cm->estimate_inr ?? 0) }}
                    </div>
                </div>
            @empty
                <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px">
                    No custom commissions logged.
                </div>
            @endforelse
        </div>

        <!-- Recent Inquiries -->
        <div class="card-custom">
            <div class="card-title">
                <span>Atelier Enquiries</span>
                <a href="{{ route('admin.enquiries.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">View All</a>
            </div>

            @forelse($recentEnquiries as $eq)
                <div style="padding:10px 0;border-bottom:1px solid var(--line)">
                    <div style="display:flex;justify-content:space-between;margin-bottom:3px">
                        <span class="pill" style="background:var(--ivory-100);font-size:10.5px">{{ strtoupper($eq->kind) }}</span>
                        <span style="font-size:11px;color:var(--muted)">{{ $eq->created_at->diffForHumans() }}</span>
                    </div>
                    <div style="font-weight:500;font-size:13px">{{ $eq->name ?: 'Client' }}</div>
                    <div style="font-size:11.5px;color:var(--muted)">{{ $eq->email ?: $eq->phone }}</div>
                </div>
            @empty
                <div style="padding:20px;text-align:center;color:var(--muted);font-size:13px">
                    No inquiries pending.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
