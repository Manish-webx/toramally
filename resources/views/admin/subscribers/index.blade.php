@extends('admin.layout', ['title' => 'Subscribers', 'header' => 'Letters from the House (Subscribers)'])

@section('content')
<div class="card-custom">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px;flex-wrap:wrap;gap:12px">
        <div>
            <span style="font-size:16px;font-weight:600;color:var(--green-900)">Total Audience: {{ $subscribers->total() }} subscribers</span>
        </div>
        <a href="{{ route('admin.subscribers.index') }}?export=csv" class="btn-atelier btn-atelier-primary btn-atelier-sm">
            📥 Export to CSV
        </a>
    </div>

    <div class="table-responsive">
        <table class="custom-table">
            <thead>
                <tr>
                    <th>Subscriber Email</th>
                    <th>Acquisition Source</th>
                    <th>Status</th>
                    <th>Joined On</th>
                </tr>
            </thead>
            <tbody>
                @forelse($subscribers as $sub)
                    <tr>
                        <td><strong>{{ $sub->email }}</strong></td>
                        <td><span class="pill" style="background:var(--ivory-100)">{{ $sub->source ?: 'footer' }}</span></td>
                        <td><span class="pill pill-delivered">{{ ucfirst($sub->status) }}</span></td>
                        <td style="font-size:12px;color:var(--muted)">{{ $sub->created_at->format('d M Y, h:i A') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center;padding:36px;color:var(--muted)">
                            No newsletter subscribers recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top:20px">
        {{ $subscribers->links() }}
    </div>
</div>
@endsection
