<form data-form="{{ $kind }}" novalidate enctype="multipart/form-data">
  <input class="hp hp-input" type="text" name="website" tabindex="-1" autocomplete="off" aria-hidden="true">
  @foreach ($fields as [$name, $label, $type, $opts, $req])
    <label class="field"><span>{{ $label }}{{ $req ? '' : ' (optional)' }}</span>
      @if ($type === 'select')
        <select name="{{ $name }}"{{ $req ? ' required' : '' }}><option value=""></option>@foreach ($opts as $o)<option>{{ $o }}</option>@endforeach</select>
      @elseif ($type === 'textarea')
        <textarea name="{{ $name }}"{{ $req ? ' required' : '' }}></textarea>
      @else
        <input name="{{ $name }}" type="{{ $type }}"{{ $req ? ' required' : '' }}{!! $type === 'email' ? ' autocomplete="email"' : ($name === 'name' ? ' autocomplete="name"' : ($type === 'tel' ? ' autocomplete="tel"' : '')) !!}{!! $type === 'date' ? ' min="' . date('Y-m-d') . '"' : '' !!}>
      @endif
      <div class="err">{{ $type === 'email' ? 'Please enter a valid email address.' : 'Please complete this field.' }}</div>
    </label>
  @endforeach
  @if (!empty($upload))
    @include('partials.upload-field')
  @endif
  <button class="btn" type="submit">{{ $cta }}</button>
  <div class="notice ok form-msg" data-formok tabindex="-1" hidden></div>
</form>
