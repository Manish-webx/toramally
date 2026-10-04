@extends('layouts.app')

@section('content')
<section class="section" style="padding: 60px 0 100px;">
  <div class="wrap" style="max-width: 980px; margin: 0 auto;">
    @include('partials.crumbs', ['crumbs' => [
      ['h' => url('/'), 't' => 'Home'],
      ['h' => '', 't' => 'My Account']
    ]])

    <div class="row" style="justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 1px solid var(--line); padding-bottom: 24px;">
      <div>
        <p class="label brass">Client Account</p>
        <h1 class="h1" style="margin-top: 6px;">Welcome, {{ $customer->first_name ?? $customer->name }}</h1>
        <p class="muted" style="margin-top: 4px;">Member of the House of Tōramally since {{ $customer->created_at ? $customer->created_at->format('F Y') : '2026' }}</p>
      </div>
      <div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn ghost">Sign Out</button>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="notice ok" style="margin-bottom: 30px;">{{ session('success') }}</div>
    @endif

    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 48px;">
      <!-- Account & Contact Profile -->
      <div style="background: var(--ivory-50, #f8f6f0); padding: 28px; border: 1px solid var(--line); border-radius: 4px;">
        <h3 class="h3" style="font-size: 20px; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Personal Information</h3>
        <dl style="display: grid; grid-template-columns: 130px 1fr; gap: 10px; font-size: 15px;">
          <dt class="muted">Name</dt>
          <dd style="font-weight: 500;">{{ $customer->full_name }}</dd>
          
          <dt class="muted">Email</dt>
          <dd>{{ $customer->email }}</dd>
          
          <dt class="muted">Phone</dt>
          <dd>{{ $customer->phone ?? 'Not specified' }}</dd>
          
          <dt class="muted">Status</dt>
          <dd><span class="brass" style="text-transform: capitalize;">{{ $customer->status ?? 'Active' }}</span></dd>
        </dl>
      </div>

      <!-- Saved Delivery Location & Address -->
      <div style="background: var(--ivory-50, #f8f6f0); padding: 28px; border: 1px solid var(--line); border-radius: 4px;">
        <h3 class="h3" style="font-size: 20px; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">Delivery Location & Address</h3>
        @if($addresses->count())
          @foreach($addresses as $addr)
            <div style="font-size: 15px; line-height: 1.6;">
              <div style="font-weight: 500; margin-bottom: 4px;">{{ $addr->name }}</div>
              <div>{{ $addr->line1 }}</div>
              @if($addr->line2)<div>{{ $addr->line2 }}</div>@endif
              <div>{{ $addr->city }}, {{ $addr->state }} - {{ $addr->postcode }}</div>
              <div>{{ $addr->country }}</div>
              @if($addr->phone)<div class="muted small" style="margin-top: 4px;">Phone: {{ $addr->phone }}</div>@endif
            </div>
          @endforeach
        @else
          <p class="muted">No saved address yet.</p>
        @endif
      </div>
    </div>

    <!-- Order History -->
    <div style="background: var(--ivory-50, #f8f6f0); padding: 32px; border: 1px solid var(--line); border-radius: 4px;">
      <h2 class="h2" style="font-size: 24px; margin-bottom: 20px; border-bottom: 1px solid var(--line); padding-bottom: 12px;">Order & Commission History</h2>

      @if($orders->count())
        <div style="display: flex; flex-direction: column; gap: 24px;">
          @foreach($orders as $order)
            <div style="background: #fff; border: 1px solid var(--line); padding: 20px; border-radius: 4px;">
              <div class="row" style="justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 12px;">
                <div>
                  <div class="label brass">Order Reference</div>
                  <div style="font-family: var(--serif); font-size: 20px; font-weight: 500;">{{ $order->order_no }}</div>
                  <div class="small muted">Placed on {{ $order->created_at ? $order->created_at->format('d M Y, h:i A') : '' }}</div>
                </div>
                <div style="text-align: right;">
                  <div class="label">Status</div>
                  <div style="font-weight: 500; color: var(--green-900, #1F3D2B);">{{ $order->status }}</div>
                  <div class="small muted">Payment: {{ $order->payment_status }}</div>
                </div>
              </div>

              <!-- Order items -->
              <div style="margin-bottom: 16px;">
                @foreach($order->items as $item)
                  <div class="row" style="justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--line);">
                    <div>
                      <span style="font-weight: 500;">{{ $item->name }}</span>
                      @if($item->colour || $item->size)
                        <span class="small muted">({{ implode(', ', array_filter([$item->colour, $item->size])) }})</span>
                      @endif
                      <span class="small muted">× {{ $item->qty }}</span>
                    </div>
                    <div style="font-weight: 500;">
                      ₹{{ number_format($item->unit_price_inr * $item->qty) }}
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="row" style="justify-content: space-between; align-items: center; padding-top: 8px;">
                <div class="small muted">
                  Delivering to: {{ $order->ship_city }}, {{ $order->ship_state }} ({{ $order->ship_postcode }})
                </div>
                <div style="font-family: var(--serif); font-size: 20px;">
                  Total: <span class="price">₹{{ number_format($order->total_inr) }}</span>
                </div>
              </div>
            </div>
          @endforeach
        </div>
      @else
        <div style="text-align: center; padding: 40px 20px;">
          <p class="serif-i" style="font-size: 22px; margin-bottom: 12px;">You have not placed any orders yet.</p>
          <p class="muted small" style="margin-bottom: 24px;">Explore our handcrafted collections or commission a bespoke pair tailored to your measurements.</p>
          <div class="row" style="justify-content: center; gap: 16px;">
            <a href="{{ route('shop') }}" class="btn">Explore Shop</a>
            <a href="{{ route('bespoke.sub', ['sub' => 'build']) }}" class="btn ghost">Commission a Pair</a>
          </div>
        </div>
      @endif
    </div>
  </div>
</section>
@endsection
