{{-- <input name="group4" type="radio" id="radio_7" class="radio-col-primary" checked />
<label for="radio_7">Primary</label> --}}

@php
$className = [$class];

if ($textColor && $textColor != '') {
    array_push($className, "text-$textColor");
}
@endphp

<input id="{{ $id == '' ? $name : $id }}" type="radio" name="{{ $name }}" class="{{ join(' ', $className) }} @error($name) is-invalid @enderror {{ $color && $color != null ? 'radio-col-' . $color : '' }}" @if ($checked) checked @endif @if ($value) value="{{ $value }}" @endif @if ($required) required @endif @if ($disabled) disabled @endif @if ($onclick) onclick="{{ $onclick }}" @endif @if ($onchange) onchange="{{ $onchange }}" @endif>

@if (($label && $label != '') || ($name && $name != ''))
    <label for="{{ $id == '' ? $name : $id }}" class="form-label {{ $textColor ? "text-$textColor" : '' }}">
        {{ $label && $label != '' ? Str::headline($label) : Str::headline($name) }}
        @if (!$hideAsterix)
            <span class="text-{{ $required ? 'danger' : 'primary' }}">*</span>
        @endif
    </label>
@endif

@error($name)
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror
