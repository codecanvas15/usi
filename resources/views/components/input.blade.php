@php
    $className = [$class, 'form-control'];
    if ($size != 'md') {
        array_push($className, "form-control-$size");
    }
    if ($rounded) {
        array_push($className, 'form-control-rounded rounded-pill');
    }
    if ($textColor && $textColor != '') {
        array_push($className, "text-$textColor");
    }
@endphp
@if (($label && $label != '') || ($name && $name != '' && $type != 'search'))
    <label for="{{ $name }}" class="form-label {{ $textColor ? "text-$textColor" : '' }}">
        {{ $label && $label != '' ? Str::headline($label) : Str::headline($name) }}
        @if (!$hideAsterix)
            <span class="text-{{ $required ? 'danger' : 'primary' }}">*</span>
        @endif
    </label>
@endif
@if ($leftIcon && $leftIcon != '')
    <div class="input-group-text">
        @if ($fontawesome)
            <i class="{{ $styleIcon }} {{ $leftIcon }} {{ $classIcon }}"></i>
        @else
            <i data-feather="{{ $leftIcon }}" class="{{ $classIcon }}"></i>
        @endif
@endif

@if ($rightIcon && $rightIcon != '')
    <div class="input-group-text">
@endif

<input id="{{ $id ?? $name }}" type="{{ $type }}" name="{{ $name }}" class="{{ join(' ', $className) }} @error($name) is-invalid @enderror" @if ($value != '' || old($name)) value="{!! old($name) ?? $value !!}" @endif @if ($placeholder || $label || $name) @if ($placeholder && $placeholder != '') placeholder="{{ $placeholder }}" @endif @if ($label && $label != '' && $placeholder == '') placeholder="{{ Str::headline($label) }}" @endif @if ($name && $name != '' && $placeholder == '' && $label == '') placeholder="{{ Str::headline($label) }}" @endif @endif
@if ($autocomplete) autocomplete="{{ $autocomplete }}" @endif
@if ($required) required @endif
@if ($autofocus) autofocus @endif
@if ($disabled) disabled @endif
@if ($onclick) onclick="{{ $onclick }}" @endif
@if ($onchange) onchange="{{ $onchange }}" @endif
@if ($onkeyup) onkeyup="{{ $onkeyup }}" @endif
@if ($onblur) onblur="{{ $onblur }}" @endif
@if ($accept) accept="{{ $accept }}" @endif
@if ($readonly) readonly @endif
>

@if ($leftIcon && $leftIcon != '')
    </div>
@endif

@if ($rightIcon && $rightIcon != '')
    @if ($fontawesome)
        <div class="ml-5">
            <i class="{{ $styleIcon }} {{ $rightIcon }} {{ $classIcon }}"></i>
        </div>
    @else
        <i data-feather="{{ $rightIcon }}" class="{{ $classIcon }}"></i>
    @endif
    </div>
@endif
@error($name)
    <div class="text-danger mt-1">{{ $message }}</div>
@enderror

@isset($useCustomError)
    <div class="text-{{ $useCustomErrorColor }} mt-1" id="error-message-for-{{ $id ?? $name }}"></div>
@enderror

@if ($helpers != '')
    <small class="text-primary mt-1">{{ $helpers }}</small>
@endif
