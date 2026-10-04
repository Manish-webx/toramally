@extends('layouts.app')

@section('content')
<section class="section" style="padding: 60px 0 100px;">
  <div class="wrap" style="max-width: 520px; margin: 0 auto;">
    @include('partials.crumbs', ['crumbs' => [
      ['h' => url('/'), 't' => 'Home'],
      ['h' => '', 't' => 'Sign In']
    ]])

    <div style="text-align: center; margin-bottom: 36px;">
      <p class="label brass">The House of Tōramally</p>
      <h1 class="h1" style="margin-top: 8px;">Sign In</h1>
      <p class="muted" style="margin-top: 8px;">Access your saved measurements, commissions and orders.</p>
    </div>

    @if(session('success'))
      <div class="notice ok" style="margin-bottom: 24px;">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="notice" style="margin-bottom: 24px; border-color: #8C2424; background: rgba(140, 36, 36, 0.05); color: #8C2424;">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" style="background: var(--ivory-50, #f8f6f0); padding: 32px 28px; border: 1px solid var(--line); border-radius: 4px;">
      @csrf

      <label class="field @error('email') bad @enderror" style="margin-bottom: 18px;">
        <span>Email Address *</span>
        <input type="email" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus placeholder="name@domain.com">
        @error('email')
          <div class="err" style="display:block;">{{ $message }}</div>
        @enderror
      </label>

      <label class="field @error('password') bad @enderror" style="margin-bottom: 18px;">
        <span>Password *</span>
        <input type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
        @error('password')
          <div class="err" style="display:block;">{{ $message }}</div>
        @enderror
      </label>

      <div class="row" style="justify-content: space-between; align-items: center; margin-bottom: 24px;">
        <label class="row" style="align-items: center; gap: 8px; font-size: 14px; cursor: pointer;">
          <input type="checkbox" name="remember" value="1" checked style="accent-color: var(--green-900, #1F3D2B); width: 16px; height: 16px;">
          <span>Remember me</span>
        </label>
      </div>

      <button type="submit" class="btn full" style="margin-bottom: 20px;">Sign In to Account</button>

      <div style="text-align: center; border-top: 1px solid var(--line); padding-top: 20px;">
        <p class="small muted" style="margin-bottom: 10px;">New to Tōramally?</p>
        <a href="{{ route('register') }}" class="btn ghost full">Create an Account</a>
      </div>
    </form>
  </div>
</section>
@endsection
