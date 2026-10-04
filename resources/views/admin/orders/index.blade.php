@extends('admin.layout', ['title' => 'Client Orders', 'header' => 'Orders & Workshop Requests'])

@section('content')
<div class="card-custom">
    <!-- Status Filter Tabs -->
    <div style="display:flex;gap:8px;overflow-x:auto;padding-bottom:12px;margin-bottom:16px;border-bottom:1px solid var(--line)">
        <a href="{{ route('admin.orders.index') }}" class="btn-atelier {{ !request('status') ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            All ({{ $statusCounts['all'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=Order+Placed" class="btn-atelier {{ request('status') === 'Order Placed' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            New Placed ({{ $statusCounts['placed'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=Confirmed" class="btn-atelier {{ request('status') === 'Confirmed' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            Confirmed ({{ $statusCounts['confirmed'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=In+Crafting" class="btn-atelier {{ request('status') === 'In Crafting' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            In Crafting ({{ $statusCounts['crafting'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=Shipped" class="btn-atelier {{ request('status') === 'Shipped' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            Shipped ({{ $statusCounts['shipped'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=Delivered" class="btn-atelier {{ request('status') === 'Delivered' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            Delivered ({{ $statusCounts['delivered'] }})
        </a>
        <a href="{{ route('admin.orders.index') }}?status=Cancelled" class="btn-atelier {{ request('status') === 'Cancelled' ? 'btn-atelier-primary' : 'btn-atelier-secondary' }} btn-atelier-sm">
            Cancelled ({{ $statusCounts['cancelled'] }})
        </a>
    </div>

    <!-- Toolbar Filters -->
    <form action="{{ route('admin.orders.index') }}" method="GET" class="toolbar-filter">
        <div class="toolbar-group" style="flex:1;max-width:480px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search order #, client name, email, phone..." value="{{ request('search') }}">
        </div>

        <div class="toolbar-group">
            <select name="payment_status" class="form-select-custom" style="width:auto">
                <option value="">All Payment States</option>
                <option value="pending" {{ request('payment_status')==='pending'?'selected':'' }}>Payment Pending / Unpaid</option>
                <option value="paid" {{ request('payment_status')==='paid'?'selected':'' }}>Paid</option>
                <option value="refunded" {{ request('payment_status')==='refunded'?'selected':'' }}>Refunded</option>
            </select>

            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['search', 'status', 'payment_status']))
                <a href="{{ route('admin.orders.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </div>
    </form>

    <!-- Orders Table -->
    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Order Ref</th>
                    <th>Client / Contact</th>
                    <th>Delivery City</th>
                    <th>Pairs</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Placed On</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr>
                        <td>
                            <strong><a href="{{ route('admin.orders.show', $order->id) }}" style="color:var(--green-900);text-decoration:none">{{ $order->order_no }}</a></strong>
                            @if($order->is_international)
                                <div style="font-size:10.5px;color:var(--brass-500);font-weight:600">INTERNATIONAL</div>
                            @endif
                        </td>
                        <td>
                            <div style="font-weight:500">{{ $order->ship_name ?: ($order->customer ? $order->customer->full_name : 'Guest') }}</div>
                            <div style="font-size:11.5px;color:var(--muted)">{{ $order->email }}</div>
                            @if($order->phone)
                                <div style="font-size:11.5px;color:var(--muted)">
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $order->phone) }}" target="_blank" style="color:#25D366;text-decoration:none">✆ {{ $order->phone }}</a>
                                </div>
                            @endif
                        </td>
                        <td>
                            {{ $order->ship_city ?: '-' }}
                            @if($order->ship_country && $order->ship_country !== 'India')
                                , {{ $order->ship_country }}
                            @endif
                        </td>
                        <td>
                            <span class="pill" style="background:var(--ivory-100)">{{ $order->items->sum('qty') }} pair(s)</span>
                        </td>
                        <td>
                            <strong>₹{{ number_format($order->total_inr) }}</strong>
                            @if($order->currency && $order->currency !== 'INR')
                                <div style="font-size:11px;color:var(--muted)">{{ $order->currency }} {{ number_format($order->total_charged) }}</div>
                            @endif
                        </td>
                        <td>
                            @if(strtolower($order->payment_status) === 'paid')
                                <span class="pill pill-paid">Paid</span>
                            @else
                                <span class="pill pill-unpaid">{{ ucfirst($order->payment_status) }}</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $s = strtolower($order->status);
                                $class = 'pill-placed';
                                if(str_contains($s, 'confirm')) $class = 'pill-confirmed';
                                elseif(str_contains($s, 'craft') || str_contains($s, 'product')) $class = 'pill-crafting';
                                elseif(str_contains($s, 'ship')) $class = 'pill-shipped';
                                elseif(str_contains($s, 'deliver')) $class = 'pill-delivered';
                                elseif(str_contains($s, 'cancel')) $class = 'pill-cancelled';
                            @endphp
                            <span class="pill {{ $class }}">{{ $order->status }}</span>
                        </td>
                        <td style="font-size:12px;color:var(--muted)">
                            {{ $order->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <div style="display:flex;gap:4px">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">Manage</a>
                                <a href="{{ route('admin.orders.invoice', $order->id) }}" target="_blank" class="btn-atelier btn-atelier-secondary btn-atelier-sm" title="Print Invoice">🖨</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:36px;color:var(--muted)">
                            No client orders found matching the filter criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $orders->links() }}
    </div>
</div>
@endsection
