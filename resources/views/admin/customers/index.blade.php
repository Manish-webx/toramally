@extends('admin.layout', ['title' => 'Patrons & Customers', 'header' => 'Patrons & Client Roster'])

@section('content')
<div class="card-custom">
    <form action="{{ route('admin.customers.index') }}" method="GET" class="toolbar-filter">
        <div class="toolbar-group" style="flex:1;max-width:480px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search customer by name, email, phone..." value="{{ request('search') }}">
        </div>
        <div class="toolbar-group">
            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Search</button>
            @if(request('search'))
                <a href="{{ route('admin.customers.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Patron Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Orders</th>
                    <th>Total Spent</th>
                    <th>Registered On</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr>
                        <td>
                            <strong><a href="{{ route('admin.customers.show', $customer->id) }}" style="color:var(--green-900);text-decoration:none">{{ $customer->full_name }}</a></strong>
                            @if($customer->is_patron)
                                <span class="pill" style="background:var(--brass-100);color:var(--brass-600);border:1px solid rgba(156,122,60,0.3);margin-left:4px">Patron</span>
                            @endif
                        </td>
                        <td>{{ $customer->email }}</td>
                        <td>
                            @if($customer->phone)
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $customer->phone) }}" target="_blank" style="color:#25D366;text-decoration:none">✆ {{ $customer->phone }}</a>
                            @else
                                <span style="color:var(--muted)">-</span>
                            @endif
                        </td>
                        <td>
                            <span class="pill" style="background:var(--ivory-100)">{{ $customer->orders->count() }} order(s)</span>
                        </td>
                        <td>
                            <strong>₹{{ number_format($customer->orders->sum('total_inr')) }}</strong>
                        </td>
                        <td style="font-size:12px;color:var(--muted)">
                            {{ $customer->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.customers.show', $customer->id) }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">View Profile</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:var(--muted)">
                            No registered patrons or customers found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $customers->links() }}
    </div>
</div>
@endsection
