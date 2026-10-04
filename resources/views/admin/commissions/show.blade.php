@extends('admin.layout', ['title' => 'Commission #' . $commission->ref, 'header' => 'Bespoke Commission: #' . $commission->ref])

@section('content')
<div style="margin-bottom:20px;display:flex;justify-content:space-between;align-items:center">
    <a href="{{ route('admin.commissions.index') }}" class="btn-atelier btn-atelier-secondary btn-atelier-sm">← Back to Commissions</a>
    @if(preg_match('/\d{10}/', $commission->contact))
        <a href="https://wa.me/{{ preg_replace('/\D/', '', $commission->contact) }}?text={{ urlencode('Hello ' . $commission->name . ', regarding your bespoke commission ' . $commission->ref . ' with Tōramally:') }}" target="_blank" class="btn-atelier btn-atelier-brass btn-atelier-sm">
            Discuss via WhatsApp
        </a>
    @endif
</div>

<div class="row g-4">
    <!-- Left Column: Commission Specifications -->
    <div class="col-12 col-lg-8">
        <div class="card-custom">
            <div class="card-title">Bespoke Design Specifications</div>

            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <div class="stat-label">Silhouette</div>
                    <div style="font-weight:600;font-size:16px">{{ $build['sil'] ?? 'Not chosen' }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Colour Palette</div>
                    <div style="font-weight:600;font-size:16px">
                        {{ $build['colour'] ?? 'Natural' }}
                        @if(!empty($build['hex'])) ({{ $build['hex'] }}) @endif
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Craft Ladder Level</div>
                    <div style="font-weight:600;font-size:16px">{{ ucfirst($build['craft'] ?? 'patina') }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Artwork Choice</div>
                    <div style="font-weight:600;font-size:16px">
                        @if(!empty($build['custom']))
                            Custom Artwork (Proof Required)
                        @else
                            {{ $build['art'] ?: 'House Pattern / None' }}
                        @endif
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Monogram / Initials</div>
                    <div style="font-weight:600;font-size:16px">
                        {{ $build['initials'] ?: 'None' }}
                        @if(!empty($build['gold'])) (Gold) @endif
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Sole Monogram</div>
                    <div style="font-weight:600;font-size:16px">{{ !empty($build['nails']) ? 'Brass Nails Monogram' : 'Standard' }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Size Specification</div>
                    <div style="font-weight:600;font-size:16px">{{ $build['size'] ?: 'To confirm with client' }}</div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-label">Builder Estimated INR</div>
                    <div style="font-weight:600;font-size:18px;color:var(--brass-500)">₹{{ number_format($commission->estimate_inr ?? 0) }}</div>
                </div>
            </div>

            @if(!empty($build['notes']))
                <div style="margin-top:20px;padding:16px;background:var(--ivory-100);border-radius:6px;border:1px solid var(--line)">
                    <div class="stat-label">Client Notes &amp; Inspiration:</div>
                    <div>{{ $build['notes'] }}</div>
                </div>
            @endif

            @if($uploads->count() > 0)
                <div style="margin-top:24px">
                    <div class="stat-label">Client Uploaded References / Artwork:</div>
                    <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:8px">
                        @foreach($uploads as $up)
                            <a href="{{ asset($up->path) }}" target="_blank" class="btn-atelier btn-atelier-secondary btn-atelier-sm">
                                📎 {{ $up->original_name ?: basename($up->path) }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Status & Quote Updater -->
    <div class="col-12 col-lg-4">
        <div class="card-custom">
            <div class="card-title">Client Details</div>
            <div style="margin-bottom:12px">
                <div class="stat-label">Client Name</div>
                <div style="font-weight:600;font-size:16px;color:var(--green-900)">{{ $commission->name }}</div>
            </div>
            <div style="margin-bottom:12px">
                <div class="stat-label">Contact Channel</div>
                <div style="font-weight:500">{{ $commission->contact }}</div>
            </div>
            <div>
                <div class="stat-label">Submitted On</div>
                <div style="color:var(--muted)">{{ $commission->created_at->format('d F Y, h:i A') }}</div>
            </div>
        </div>

        <div class="card-custom">
            <div class="card-title">Atelier Quote &amp; Status</div>

            <form action="{{ route('admin.commissions.update', $commission->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label-custom">Commission Status</label>
                    <select name="status" class="form-select-custom" required>
                        <option value="Enquiry received" {{ $commission->status==='Enquiry received'?'selected':'' }}>Enquiry received</option>
                        <option value="Under Review" {{ $commission->status==='Under Review'?'selected':'' }}>Under Review</option>
                        <option value="Quote shared" {{ $commission->status==='Quote shared'?'selected':'' }}>Quote shared</option>
                        <option value="Proof approved" {{ $commission->status==='Proof approved'?'selected':'' }}>Proof approved</option>
                        <option value="In Making" {{ $commission->status==='In Making'?'selected':'' }}>In Making</option>
                        <option value="Completed" {{ $commission->status==='Completed'?'selected':'' }}>Completed</option>
                        <option value="Archived" {{ $commission->status==='Archived'?'selected':'' }}>Archived</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Official Quote (INR)</label>
                    <input type="number" name="quote_inr" class="form-control-custom" value="{{ $commission->quote_inr }}" placeholder="e.g. 85000">
                </div>

                <div class="mb-3">
                    <label class="form-label-custom">Atelier Internal Notes</label>
                    <textarea name="admin_notes" rows="4" class="form-control-custom" placeholder="Design proof status, swatch approval, conversation summary...">{{ $commission->admin_notes }}</textarea>
                </div>

                <button type="submit" class="btn-atelier btn-atelier-primary full" style="width:100%;justify-content:center">Save Commission</button>
            </form>
        </div>
    </div>
</div>
@endsection
