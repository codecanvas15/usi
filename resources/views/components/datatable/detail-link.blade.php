@php
    $permission = isset($permission_name) ? $permission_name : $main;
@endphp

@can("view $permission")
    <a href="{{ route("admin.$main.show", $row) }}" class="text-primary text-decoration-underline hover_text-dark" target="_blank">{!! $field !!}</a>
@elsecan("view $permission")
    {{ $field }}
@endcan
