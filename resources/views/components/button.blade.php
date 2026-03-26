@php
    $className = [$class, $block ? 'w-full' : ''];
    
    // tag
    array_push($className, 'btn');
    if ($link && $link != '') {
        $tag = 'a';
    } elseif ($badge) {
        $tag = 'span';
    } else {
        $tag = 'button';
    }
    
    // class
    if ($size != 'md') {
        array_push($className, "btn-$size");
    }
    if ($shadow) {
        array_push($className, 'btn-elevated');
    }
    if ($rounded) {
        array_push($className, 'btn-rounded');
    }
    if ($color && $color != '') {
        if ($outline) {
            array_push($className, "btn-outline-$color");
        } elseif ($soft) {
            array_push($className, "btn-$color-light");
        } elseif ($gradient) {
            array_push($className, "bg-gradient-$color");
        } else {
            array_push($className, "btn-$color");
        }
    }
    if ($textColor && $textColor != '') {
        array_push($className, "text-$textColor");
    }
    
@endphp
<{{ $tag }} @if ($link && $link != '') href="{{ $link }}" {{ $target != '' ? "target='_blank'" : '' }}
    @else
        type="{{ $type }}" @endif class="{{ join(' ', $className) }}" @if ($id) id="{{ $id }}" @endif @if ($disabled) disabled @endif @if ($dataToggle) data-bs-toggle="{{ $dataToggle }}" @endif @if ($dataTarget) data-bs-target="{{ $dataTarget }}" @endif @if ($dataDismiss) data-bs-dismiss="{{ $dataDismiss }}" @endif @isset($onclick) onclick="{{ $onclick }}"   @endisset @isset($onchange) onchange="{{ $onchange }}" @endisset>
    @if ($icon && $icon != '' && $iconRight == '')
        @if ($fontawesome)
            <i class="{{ $styleIcon }} fa-{{ $icon }} {{ $label ? 'mr-2' : '' }}"></i>
        @else
            <i data-feather="{{ $icon }}" class="w-4 h-4 mr-2"></i>
        @endif
    @endif
    {{ Str::headline($label) }}
    @if ($icon && $icon != '' && $iconRight != '')
        @if ($fontawesome)
            <i class="{{ $styleIcon }} fa-{{ $icon }} {{ $label ? 'ml-2' : '' }}"></i>
        @else
            <i data-feather="{{ $icon }}" class="w-4 h-4 mr-2"></i>
        @endif
    @endif
    </{{ $tag }}>
