@extends('layouts.app')

@section('content')
<div class="wrap">
  @include('partials.crumbs', ['items' => [['Home', ''], ['Size & Fit Guide', null]]])
  <div style="margin-top:12px">
    @include('partials.page-head', [
      'title' => 'Size & Fit Guide', 
      'sub' => 'Our shoes are sized in UK sizes, which are the same as Indian sizes. Use the tables below to convert.'
    ])
  </div>

  <div style="max-width:860px;margin-bottom:32px">
    <p>Our shoes are sized in UK sizes, which are the same as Indian sizes. Use the tables below to convert. If you are between sizes, or have a wide or narrow foot, write to us before ordering — we would rather advise than exchange.</p>
  </div>

  <div class="grid g2" style="gap:32px;align-items:start;padding-bottom:48px">
    <!-- Men's Table -->
    <div class="tile" style="padding:24px">
      <h2 class="h3" style="margin-bottom:16px">Men</h2>
      <div class="scrollx">
        <table class="sz" style="width:100%;text-align:center">
          <thead>
            <tr>
              <th style="text-align:center">UK / India</th>
              <th style="text-align:center">EU</th>
              <th style="text-align:center">US</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>6</td><td>40</td><td>7</td></tr>
            <tr><td>7</td><td>41</td><td>8</td></tr>
            <tr><td>8</td><td>42</td><td>9</td></tr>
            <tr><td>9</td><td>43</td><td>10</td></tr>
            <tr><td>10</td><td>44</td><td>11</td></tr>
            <tr><td>11</td><td>45</td><td>12</td></tr>
            <tr><td>12</td><td>46</td><td>13</td></tr>
          </tbody>
        </table>
      </div>
    </div>

    <!-- Women's Table -->
    <div class="tile" style="padding:24px">
      <h2 class="h3" style="margin-bottom:16px">Women</h2>
      <div class="scrollx">
        <table class="sz" style="width:100%;text-align:center">
          <thead>
            <tr>
              <th style="text-align:center">UK / India</th>
              <th style="text-align:center">EU</th>
              <th style="text-align:center">US</th>
            </tr>
          </thead>
          <tbody>
            <tr><td>3</td><td>36</td><td>5</td></tr>
            <tr><td>4</td><td>37</td><td>6</td></tr>
            <tr><td>5</td><td>38</td><td>7</td></tr>
            <tr><td>6</td><td>39</td><td>8</td></tr>
            <tr><td>7</td><td>40</td><td>9</td></tr>
            <tr><td>8</td><td>41</td><td>10</td></tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- How to measure your foot (Page 10) -->
  <div class="split" style="align-items:start;padding-bottom:96px">
    <div>
      <h2 class="h3">How to measure your foot</h2>
      <ol class="steps4" style="grid-template-columns:1fr;margin-top:16px">
        <li>
          <strong>1. Stand on a sheet of paper</strong>
          <p class="small" style="margin-top:4px">Stand on a sheet of paper in the evening, when your feet are largest, wearing the socks you would wear with the shoes.</p>
        </li>
        <li>
          <strong>2. Mark toe and heel</strong>
          <p class="small" style="margin-top:4px">Mark the tip of your longest toe and the back of your heel.</p>
        </li>
        <li>
          <strong>3. Measure both feet</strong>
          <p class="small" style="margin-top:4px">Measure the distance in centimetres. Measure both feet and use the longer.</p>
        </li>
      </ol>
      <div style="margin-top:24px">
        @include('partials.wa-button', ['label' => 'Help me find my size', 'text' => 'Hello Tōramally, I would like advice on finding my correct size. My foot measures ___ cm.'])
      </div>
    </div>
    <div class="notice" style="background:var(--ivory-100,#f7f5f0);padding:28px;border-radius:4px">
      <h3 class="h3">Personal Fitting Advice</h3>
      <p style="margin-top:12px">If you are unsure of your last, or between sizes, our bootmakers are happy to assist. Write to us at <a href="mailto:care@toramally.com">care@toramally.com</a> or WhatsApp us with your usual size in other footwear.</p>
      <div class="row" style="margin-top:16px">
        <a class="btn ghost" href="{{ url('appointments') }}">Book a fitting appointment</a>
      </div>
    </div>
  </div>
</div>
@endsection
