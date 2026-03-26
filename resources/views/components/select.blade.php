@php
    $className = [$class, 'form-control '];
    if ($size != 'md') {
        array_push($className, "form-control-$size");
    }
    if ($rounded) {
        array_push($className, 'form-control');
    }
    if ($textColor && $textColor != '') {
        array_push($className, "text-$textColor");
    }
    
    if ($selectType && $selectType != '') {
        array_push($className, $selectType);
    } else {
        array_push($className, 'select2');
    }
@endphp

<div class="form-group">

    @if (!$hideLabel)
        @if (($label && $label != '') || ($name && $name != ''))
            <label for="{{ $name }}" class="form-label {{ $textColor ? "text-$textColor" : '' }}">
                {{ $label && $label != '' ? Str::headline($label) : Str::headline($name) }}
                @if (!$hideAsterix)
                    <span class="text-{{ $required ? 'danger' : 'primary' }}">*</span>
                @endif
            </label>

            @if ($useBr)
                <br>
            @endif
        @endif
    @endif

    @if (!$hasError)
        <select id="{{ $id ?? $name }}" name="{{ $name }}" class="{{ join(' ', $className) }} @error($name) is-invalid @enderror" @if ($dataSpecial && $dataSpecial != '') data-special="{{ $dataSpecial }}" @endif @if ($value) value="{{ $value }}" @endif @if ($required) required @endif @if ($autofocus) autofocus @endif @if ($disabled) disabled @endif @if ($onclick) onclick="{{ $onclick }}" @endif @if ($onchange) onchange="{{ $onchange }}" @endif @if ($multiple != '') multiple @endif>
            {{ $slot }}
        </select>
    @else
        <div @if ($errorBorderId) id="{{ $errorBorderId }}" @endif class="border border-danger">
            <select id="{{ $id ?? $name }}" name="{{ $name }}" class="{{ join(' ', $className) }} @error($name) is-invalid @enderror" @if ($dataSpecial && $dataSpecial != '') data-special="{{ $dataSpecial }}" @endif @if ($value) value="{{ $value }}" @endif @if ($required) required @endif @if ($autofocus) autofocus @endif @if ($disabled) disabled @endif @if ($onclick) onclick="{{ $onclick }}" @endif @if ($onchange) onchange="{{ $onchange }}" @endif @if ($multiple != '') multiple @endif>
                {{ $slot }}
            </select>
        </div>
        <div @if ($errorMessageId) id="{{ $errorMessageId }}" @endif class="text-danger mt-1">{{ $errorMsg }}</div>
    @endif

    @if ($helpers != '')
        <small class="text-primary mt-1">{{ $helpers }}</small>
    @endif

    @error($name)
        <div class="text-danger mt-1">{{ $message }}</div>
    @enderror
</div>
