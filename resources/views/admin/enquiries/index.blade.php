@extends('admin.layout', ['title' => 'Appointments & Enquiries', 'header' => 'Client Inquiries & Appointments'])

@section('content')
<div class="card-custom">
    <!-- Toolbar Filters -->
    <form action="{{ route('admin.enquiries.index') }}" method="GET" class="toolbar-filter">
        <div class="toolbar-group">
            <select name="kind" class="form-select-custom" style="width:auto">
                <option value="">All Inquiry Types</option>
                <option value="appointment" {{ request('kind')==='appointment'?'selected':'' }}>Kolkata Appointments</option>
                <option value="contact" {{ request('kind')==='contact'?'selected':'' }}>General Contact</option>
                <option value="restoration" {{ request('kind')==='restoration'?'selected':'' }}>Restoration &amp; Shoe Shine</option>
                <option value="wedding" {{ request('kind')==='wedding'?'selected':'' }}>Wedding Consultations</option>
            </select>

            <select name="status" class="form-select-custom" style="width:auto">
                <option value="">All Statuses</option>
                <option value="New" {{ request('status')==='New'?'selected':'' }}>New</option>
                <option value="In Review" {{ request('status')==='In Review'?'selected':'' }}>In Review</option>
                <option value="Resolved" {{ request('status')==='Resolved'?'selected':'' }}>Resolved / Contacted</option>
            </select>

            <button type="submit" class="btn-atelier btn-atelier-primary btn-atelier-sm">Filter</button>
            @if(request()->hasAny(['kind', 'status']))
                <a href="{{ route('admin.enquiries.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">Clear</a>
            @endif
        </div>
    </form>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Client Name</th>
                    <th>Contact Details</th>
                    <th>Message / Request Payload</th>
                    <th>Received On</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($enquiries as $eq)
                    @php $data = json_decode($eq->payload_json, true) ?: []; @endphp
                    <tr>
                        <td>
                            <span class="pill" style="background:var(--ivory-100);font-weight:600">
                                {{ strtoupper($eq->kind) }}
                            </span>
                        </td>
                        <td><strong>{{ $eq->name ?: ($data['name'] ?? 'Client') }}</strong></td>
                        <td>
                            <div>{{ $eq->email ?: ($data['email'] ?? '-') }}</div>
                            <div>{{ $eq->phone ?: ($data['phone'] ?? ($data['contact'] ?? '')) }}</div>
                            @php $num = $eq->phone ?: ($data['phone'] ?? ($data['contact'] ?? '')); @endphp
                            @if($num && preg_match('/\d{10}/', $num))
                                <a href="https://wa.me/{{ preg_replace('/\D/', '', $num) }}" target="_blank" style="color:#25D366;font-size:12px;text-decoration:none">✆ WhatsApp</a>
                            @endif
                        </td>
                        <td style="max-width:320px">
                            @if(!empty($data['message']))
                                <div>{{ $data['message'] }}</div>
                            @elseif(!empty($data['notes']))
                                <div>{{ $data['notes'] }}</div>
                            @elseif(!empty($data['date']))
                                <div><strong>Date requested:</strong> {{ $data['date'] }} @if(!empty($data['time'])) at {{ $data['time'] }} @endif</div>
                            @else
                                <div style="font-size:12px;color:var(--muted)">{{ json_encode($data) }}</div>
                            @endif
                        </td>
                        <td style="font-size:12px;color:var(--muted)">{{ $eq->created_at->format('d M Y, h:i A') }}</td>
                        <td>
                            <span class="pill {{ $eq->status === 'New' ? 'pill-placed' : 'pill-confirmed' }}">
                                {{ $eq->status }}
                            </span>
                        </td>
                        <td>
                            <form action="{{ route('admin.enquiries.status', $eq->id) }}" method="POST" style="display:inline">
                                @csrf
                                <input type="hidden" name="status" value="{{ $eq->status === 'Resolved' ? 'New' : 'Resolved' }}">
                                <button type="submit" class="btn-atelier btn-atelier-secondary btn-atelier-sm">
                                    {{ $eq->status === 'Resolved' ? 'Mark New' : 'Mark Resolved' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center;padding:36px;color:var(--muted)">
                            No inquiries recorded in this category.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $enquiries->links() }}
    </div>
</div>
@endsection
