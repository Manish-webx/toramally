@extends('layouts.app')

@section('content')
<section class="section" style="padding: 60px 0 100px;">
  <div class="wrap" style="max-width: 1040px; margin: 0 auto;">
    @include('partials.crumbs', ['crumbs' => [
      ['h' => url('/'), 't' => 'Home'],
      ['h' => '', 't' => 'My Account']
    ]])

    <div class="row" style="justify-content: space-between; align-items: flex-end; margin-bottom: 40px; border-bottom: 1px solid var(--line); padding-bottom: 24px;">
      <div>
        <p class="label brass">Client Dossier</p>
        <h1 class="h1" style="margin-top: 6px;">Welcome, {{ $customer->first_name ?? $customer->name }}</h1>
        <p class="muted" style="margin-top: 4px;">Patron of the House of Tōramally since {{ $customer->created_at ? $customer->created_at->format('F Y') : '2026' }}</p>
      </div>
      <div>
        <form method="POST" action="{{ route('logout') }}" style="display:inline;">
          @csrf
          <button type="submit" class="btn ghost">Sign Out</button>
        </form>
      </div>
    </div>

    @if(session('success'))
      <div class="notice ok" style="margin-bottom: 30px; background: #eef7ed; border: 1px solid #7bc676; padding: 14px 20px; border-radius: 4px; color: #1e561a;">
        {{ session('success') }}
      </div>
    @endif

    @if($errors->any())
      <div class="notice err" style="margin-bottom: 30px; background: #fdf2f2; border: 1px solid #f09898; padding: 14px 20px; border-radius: 4px; color: #8e1f1f;">
        <ul style="margin: 0; padding-left: 20px;">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    @php
      $defaultAddr = $customer->defaultAddress ?? $addresses->first();
      $showProfileEdit = session('error_tab') === 'profile';
      $showAddressEdit = session('error_tab') === 'address';
    @endphp

    <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 32px; margin-bottom: 48px; align-items: start;">
      
      <!-- Personal Information Card -->
      <div style="background: var(--ivory-50, #f8f6f0); padding: 28px; border: 1px solid var(--line); border-radius: 4px;">
        <div class="row" style="justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 10px;">
          <h3 class="h3" style="font-size: 20px; margin: 0;">Personal Information</h3>
          <button type="button" id="btn-toggle-profile" onclick="toggleSection('profile')" class="btn ghost" style="padding: 6px 14px; font-size: 13px;">
            {{ $showProfileEdit ? 'Cancel' : 'Edit Details' }}
          </button>
        </div>

        <!-- View Mode -->
        <div id="profile-view" style="{{ $showProfileEdit ? 'display:none;' : '' }}">
          <dl style="display: grid; grid-template-columns: 110px 1fr; gap: 12px; font-size: 15px; margin: 0;">
            <dt class="muted">Name</dt>
            <dd style="font-weight: 500; margin: 0;">{{ $customer->full_name }}</dd>
            
            <dt class="muted">Email</dt>
            <dd style="margin: 0;">{{ $customer->email }}</dd>
            
            <dt class="muted">Phone</dt>
            <dd style="margin: 0;">{{ $customer->phone ?? 'Not specified' }}</dd>
            
            <dt class="muted">Status</dt>
            <dd style="margin: 0;"><span class="brass" style="text-transform: capitalize;">{{ $customer->status ?? 'Active' }}</span></dd>
          </dl>
        </div>

        <!-- Edit Form -->
        <form id="profile-edit-form" method="POST" action="{{ route('account.profile') }}" style="{{ $showProfileEdit ? '' : 'display:none;' }}">
          @csrf
          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">First Name *</label>
              <input type="text" name="first_name" value="{{ old('first_name', $customer->first_name) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Last Name *</label>
              <input type="text" name="last_name" value="{{ old('last_name', $customer->last_name) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
          </div>

          <div style="margin-bottom: 14px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Email (Account Identifier)</label>
            <input type="email" value="{{ $customer->email }}" disabled style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #eee; color: #666; cursor: not-allowed; box-sizing: border-box;">
            <span class="small muted" style="font-size: 11px;">To change your email address, please reach out to atelier support.</span>
          </div>

          <div style="margin-bottom: 14px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Phone Number *</label>
            <input type="tel" name="phone" value="{{ old('phone', $customer->phone) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 18px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">New Password (Optional)</label>
            <input type="password" name="password" placeholder="Leave blank to keep existing password" style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div class="row" style="gap: 10px; justify-content: flex-start;">
            <button type="submit" class="btn" style="padding: 8px 18px; font-size: 13px;">Save Changes</button>
            <button type="button" onclick="toggleSection('profile')" class="btn ghost" style="padding: 8px 14px; font-size: 13px;">Cancel</button>
          </div>
        </form>
      </div>

      <!-- Saved Delivery Location & Address Card -->
      <div style="background: var(--ivory-50, #f8f6f0); padding: 28px; border: 1px solid var(--line); border-radius: 4px;">
        <div class="row" style="justify-content: space-between; align-items: center; margin-bottom: 16px; border-bottom: 1px solid var(--line); padding-bottom: 10px;">
          <h3 class="h3" style="font-size: 20px; margin: 0;">Delivery Location & Address</h3>
          <button type="button" id="btn-toggle-address" onclick="toggleSection('address')" class="btn ghost" style="padding: 6px 14px; font-size: 13px;">
            {{ $showAddressEdit ? 'Cancel' : ($defaultAddr ? 'Edit Address' : '+ Add Address') }}
          </button>
        </div>

        <!-- View Mode -->
        <div id="address-view" style="{{ $showAddressEdit ? 'display:none;' : '' }}">
          @if($defaultAddr)
            <div style="font-size: 15px; line-height: 1.6;">
              <div style="font-weight: 600; margin-bottom: 4px; color: var(--green-900, #1F3D2B);">{{ $defaultAddr->name }}</div>
              <div>{{ $defaultAddr->line1 }}</div>
              @if($defaultAddr->line2)<div>{{ $defaultAddr->line2 }}</div>@endif
              <div>{{ $defaultAddr->city }}, {{ $defaultAddr->state }} - {{ $defaultAddr->postcode }}</div>
              <div>{{ $defaultAddr->country }}</div>
              @if($defaultAddr->phone)<div class="muted small" style="margin-top: 6px;">Phone: {{ $defaultAddr->phone }}</div>@endif
            </div>
          @else
            <p class="muted" style="margin-bottom: 16px;">No delivery address on record yet.</p>
            <button type="button" onclick="toggleSection('address')" class="btn ghost" style="padding: 6px 14px; font-size: 13px;">+ Add Address Now</button>
          @endif
        </div>

        <!-- Edit Form -->
        <form id="address-edit-form" method="POST" action="{{ route('account.address') }}" style="{{ $showAddressEdit ? '' : 'display:none;' }}">
          @csrf
          <div style="margin-bottom: 14px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Recipient Full Name *</label>
            <input type="text" name="name" value="{{ old('name', $defaultAddr->name ?? $customer->full_name) }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 14px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Address Line 1 *</label>
            <input type="text" name="line1" value="{{ old('line1', $defaultAddr->line1 ?? '') }}" placeholder="House/Flat No., Building, Street name" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div style="margin-bottom: 14px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Address Line 2 (Optional)</label>
            <input type="text" name="line2" value="{{ old('line2', $defaultAddr->line2 ?? '') }}" placeholder="Apartment, suite, landmark" style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">City *</label>
              <input type="text" name="city" value="{{ old('city', $defaultAddr->city ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">State *</label>
              <input type="text" name="state" value="{{ old('state', $defaultAddr->state ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
          </div>

          <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; margin-bottom: 14px;">
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">PIN / Postal Code *</label>
              <input type="text" name="postcode" value="{{ old('postcode', $defaultAddr->postcode ?? '') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
            <div>
              <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Country *</label>
              <input type="text" name="country" value="{{ old('country', $defaultAddr->country ?? 'India') }}" required style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
            </div>
          </div>

          <div style="margin-bottom: 18px;">
            <label class="label" style="font-size: 11px; display: block; margin-bottom: 4px;">Delivery Contact Phone</label>
            <input type="tel" name="phone" value="{{ old('phone', $defaultAddr->phone ?? $customer->phone) }}" style="width: 100%; padding: 8px 12px; border: 1px solid var(--line); font-size: 14px; background: #fff; box-sizing: border-box;">
          </div>

          <div class="row" style="gap: 10px; justify-content: flex-start;">
            <button type="submit" class="btn" style="padding: 8px 18px; font-size: 13px;">Save Address</button>
            <button type="button" onclick="toggleSection('address')" class="btn ghost" style="padding: 8px 14px; font-size: 13px;">Cancel</button>
          </div>
        </form>
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

<script>
function toggleSection(sec) {
  var viewEl = document.getElementById(sec + '-view');
  var formEl = document.getElementById(sec + '-edit-form');
  var btnEl = document.getElementById('btn-toggle-' + sec);

  if (!viewEl || !formEl) return;

  var isEditing = formEl.style.display !== 'none';
  if (isEditing) {
    formEl.style.display = 'none';
    viewEl.style.display = 'block';
    if (btnEl) btnEl.textContent = sec === 'profile' ? 'Edit Details' : 'Edit Address';
  } else {
    formEl.style.display = 'block';
    viewEl.style.display = 'none';
    if (btnEl) btnEl.textContent = 'Cancel';
  }
}
</script>
@endsection
