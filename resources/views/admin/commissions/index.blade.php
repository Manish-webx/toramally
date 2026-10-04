@extends('admin.layout', ['title' => 'Bespoke Commissions', 'header' => 'Bespoke Builder Commissions'])

@section('content')
<div class="card-custom">
    <!-- Toolbar Filters -->
    <form action="{{ route('admin.commissions.index') }}" method="GET" class="toolbar-filter">
        <div class="toolbar-group" style="flex:1;max-width:480px">
            <input type="text" name="search" class="form-control-custom" placeholder="Search reference code, client name, contact..." value="{{ request('search') }}">
        </div>

        <div class="toolbar-group">
            <select name="status" class="form-select-custom" style="width:auto">
                <option value="">All Statuses</option>
                <option value="Enquiry received" {{ request('status')==='Enquiry received'?'selected':'' }}>Enquiry received</option>
                <option value="Quote shared" {{ request('status')==='Quote shared'?'selected':'' }}>Quote shared</option>
                <option value="Proof approved" {{ request('status')==='Proof approved'?'selected':'' }}>Proof approved</option>
                <option value="In Making" {{ request('status')==='In Making'?'selected':'' }}>In Making</option>
                <option value="Completed" {{ request('status')==='Completed'?'selected':'' }}>Completed</option>
            </select>

            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['search', 'status']))
                <a href="{{ route('admin.commissions.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Commission Ref</th>
                    <th>Client Name</th>
                    <th>Contact</th>
                    <th>Specifications</th>
                    <th>Estimate (INR)</th>
                    <th>Quote (INR)</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($commissions as $comm)
                    @php $b = json_decode($comm->build_json, true) ?: []; @endphp
                    <tr>
                        <td>
                            <strong><a href="{{ route('admin.commissions.show', $comm->id) }}" style="color:var(--green-900);text-decoration:none">{{ $comm->ref }}</a></strong>
                        </td>
                        <td>
                            <div style="font-weight:600">{{ $comm->name }}</div>
                        </td>
                        <td>
                            <div>{{ $comm->contact }}</div>
                            @if(preg_match('/\d{10}/', $comm->contact))
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $comm->contact) }}" target="_blank" style="color:#25D366;font-size:12px;text-decoration:none">✆ WhatsApp</a>
                            @endif
                        </td>
                        <td>
                            <div><strong>{{ $b['sil'] ?? 'Silhouette' }}</strong> · {{ $b['colour'] ?? '' }}</div>
                            <div style="font-size:12px;color:var(--muted)">
                                {{ $b['craft'] ?? '' }}
                                @if(!empty($b['art'])) · Art: {{ $b['art'] }} @endif
                                @if(!empty($b['initials'])) · Initials: {{ $b['initials'] }} @endif
                            </div>
                        </td>
                        <td>
                            ₹{{ number_format($comm->estimate_inr ?? 0) }}
                        </td>
                        <td>
                            @if($comm->quote_inr)
                                <strong>₹{{ number_format($comm->quote_inr) }}</strong>
                            @else
                                <span style="color:var(--muted);font-style:italic">Unquoted</span>
                            @endif
                        </td>
                        <td>
                            <span class="pill pill-crafting">{{ $comm->status }}</span>
                        </td>
                        <td style="font-size:12px;color:var(--muted)">
                            {{ $comm->created_at->format('d M Y') }}
                        </td>
                        <td>
                            <a href="{{ route('admin.commissions.show', $comm->id) }}" class="btn-atelier btn-atelier-primary btn-atelier-sm">Review Build</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align:center;padding:36px;color:var(--muted)">
                            No bespoke builder commissions found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $commissions->links() }}
    </div>
</div>
@endsection
