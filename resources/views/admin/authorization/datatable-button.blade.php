@php
    $permission = isset($permission_name) ? $permission_name : $main;
@endphp

@can("view $permission")
    <a href="{{ route("admin.$main.show", $row) }}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">{{ $field }}</a>
@elsecan("view $permission")
    {{ $field }}
@endcan
