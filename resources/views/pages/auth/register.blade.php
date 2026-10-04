@extends('layouts.app')

@section('content')
<section class="section" style="padding: 60px 0 100px;">
  <div class="wrap" style="max-width: 680px; margin: 0 auto;">
    @include('partials.crumbs', ['crumbs' => [
      ['h' => url('/'), 't' => 'Home'],
      ['h' => '', 't' => 'Create Account']
    ]])

    <div style="text-align: center; margin-bottom: 36px;">
      <p class="label brass">The House of Tōramally</p>
      <h1 class="h1" style="margin-top: 8px;">Create Customer Account</h1>
      <p class="muted" style="margin-top: 8px;">Please register your account with your contact and delivery location details to order handcrafted pairs.</p>
    </div>

    @if($errors->any())
      <div class="notice" style="margin-bottom: 24px; border-color: #8C2424; background: rgba(140, 36, 36, 0.05); color: #8C2424;">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('register.post') }}" style="background: var(--ivory-50, #f8f6f0); padding: 36px 32px; border: 1px solid var(--line); border-radius: 4px;">
      @csrf

      <h3 class="h3" style="margin-bottom: 16px; font-size: 20px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">1. Personal Details</h3>

      <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <label class="field @error('first_name') bad @enderror">
          <span>First Name *</span>
          <input type="text" name="first_name" value="{{ old('first_name') }}" required autocomplete="given-name" placeholder="Rohan">
          @error('first_name')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
        <label class="field @error('last_name') bad @enderror">
          <span>Last Name *</span>
          <input type="text" name="last_name" value="{{ old('last_name') }}" required autocomplete="family-name" placeholder="Sharma">
          @error('last_name')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
      </div>

      <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <label class="field @error('email') bad @enderror">
          <span>Email Address *</span>
          <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="rohan@example.com">
          @error('email')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
        <label class="field @error('phone') bad @enderror">
          <span>Phone / WhatsApp *</span>
          <input type="tel" name="phone" value="{{ old('phone') }}" required autocomplete="tel" placeholder="+91 98765 43210">
          @error('phone')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
      </div>

      <label class="field @error('password') bad @enderror" style="margin-bottom: 24px;">
        <span>Create Password (minimum 6 characters) *</span>
        <input type="password" name="password" required autocomplete="new-password" placeholder="••••••••">
        @error('password')<div class="err" style="display:block;">{{ $message }}</div>@enderror
      </label>

      <h3 class="h3" style="margin-bottom: 16px; font-size: 20px; border-bottom: 1px solid var(--line); padding-bottom: 8px;">2. Delivery Location & Address</h3>

      <label class="field @error('line1') bad @enderror" style="margin-bottom: 16px;">
        <span>Address Line 1 (Flat, House No., Street, Area) *</span>
        <input type="text" name="line1" value="{{ old('line1') }}" required autocomplete="address-line1" placeholder="42 Park Street, Flat 4B">
        @error('line1')<div class="err" style="display:block;">{{ $message }}</div>@enderror
      </label>

      <label class="field @error('line2') bad @enderror" style="margin-bottom: 16px;">
        <span>Address Line 2 (Apartment, Suite, Landmark - optional)</span>
        <input type="text" name="line2" value="{{ old('line2') }}" autocomplete="address-line2" placeholder="Near Victoria Memorial">
        @error('line2')<div class="err" style="display:block;">{{ $message }}</div>@enderror
      </label>

      <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
        <label class="field @error('city') bad @enderror">
          <span>City / Town *</span>
          <input type="text" name="city" value="{{ old('city') }}" required autocomplete="address-level2" placeholder="Kolkata">
          @error('city')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
        <label class="field @error('state') bad @enderror">
          <span>State / Province *</span>
          <input type="text" name="state" value="{{ old('state') }}" required autocomplete="address-level1" placeholder="West Bengal">
          @error('state')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
      </div>

      <div class="row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <label class="field @error('postcode') bad @enderror">
          <span>Pincode / Postal Code *</span>
          <input type="text" name="postcode" value="{{ old('postcode') }}" required autocomplete="postal-code" placeholder="700016">
          @error('postcode')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
        <label class="field @error('country') bad @enderror">
          <span>Country *</span>
          <select name="country">
            <option value="India" {{ old('country', 'India') === 'India' ? 'selected' : '' }}>India</option>
            <option value="United States" {{ old('country') === 'United States' ? 'selected' : '' }}>United States</option>
            <option value="United Kingdom" {{ old('country') === 'United Kingdom' ? 'selected' : '' }}>United Kingdom</option>
            <option value="United Arab Emirates" {{ old('country') === 'United Arab Emirates' ? 'selected' : '' }}>United Arab Emirates</option>
            <option value="Singapore" {{ old('country') === 'Singapore' ? 'selected' : '' }}>Singapore</option>
            <option value="Australia" {{ old('country') === 'Australia' ? 'selected' : '' }}>Australia</option>
            <option value="Canada" {{ old('country') === 'Canada' ? 'selected' : '' }}>Canada</option>
            <option value="Other" {{ old('country') === 'Other' ? 'selected' : '' }}>Other</option>
          </select>
          @error('country')<div class="err" style="display:block;">{{ $message }}</div>@enderror
        </label>
      </div>

      <label class="row" style="align-items: center; gap: 10px; margin-bottom: 24px; cursor: pointer; font-size: 14px;">
        <input type="checkbox" name="marketing_opt_in" value="1" checked style="accent-color: var(--green-900, #1F3D2B); width: 16px; height: 16px;">
        <span>Receive discreet notices on new private commissions, bespoke crafts, and seasonal dispatches.</span>
      </label>

      <button type="submit" class="btn full" style="margin-bottom: 20px;">Complete Registration &amp; Create Account</button>

      <div style="text-align: center; border-top: 1px solid var(--line); padding-top: 20px;">
        <p class="small muted" style="margin-bottom: 10px;">Already have an account?</p>
        <a href="{{ route('login') }}" class="btn ghost full">Sign In</a>
      </div>
    </form>
  </div>
</section>
@endsection
