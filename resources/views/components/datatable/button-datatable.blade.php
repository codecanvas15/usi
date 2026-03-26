@php
    $permission = isset($permission_name) ? $permission_name : $main;
    $is_multiple_permission = $is_multiple_permission ?? false;
    if ($is_multiple_permission) {
        $split_permissions = explode('|', $permission);
        $multiple_permission_view = [];
        $multiple_permission_edit = [];
        $multiple_permission_delete = [];

        foreach ($split_permissions as $key => $split_permission) {
            array_push($multiple_permission_view, "view $split_permission");
            array_push($multiple_permission_edit, "edit $split_permission");
            array_push($multiple_permission_delete, "delete $split_permission");
        }
    }
@endphp
@if ($btn_config['detail']['display'])
    @if ($is_multiple_permission)
        @canany($multiple_permission_view)
            <x-button color='primary' icon='eye' fontawesome size="sm" link='{{ route("admin.$main.show", $row) }}' />
        @endcanany
    @else
        @can("view $permission")
            <x-button color='primary' icon='eye' fontawesome size="sm" link='{{ route("admin.$main.show", $row) }}' />
        @endcan
    @endif
@endif

@if ($btn_config['edit']['display'])
    @if ($is_multiple_permission)
        @canany($multiple_permission_edit)
            <x-button color='warning' icon='edit' fontawesome size="sm" link='{{ route("admin.$main.edit", $row) }}' />
        @endcanany
    @else
        @can("edit $permission")
            <x-button color='warning' icon='edit' fontawesome size="sm" link='{{ route("admin.$main.edit", $row) }}' />
        @endcan
    @endif
@endif

@if ($btn_config['delete']['display'])
    @if ($is_multiple_permission)
        @canany($multiple_permission_delete)
            <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
            <x-modal-delete id="delete-modal-{{ $row->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $row->id }}" />
        @endcanany
    @else
        @can("delete $permission")
            <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
            <x-modal-delete id="delete-modal-{{ $row->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $row->id }}" />
        @endcan
    @endif
@endif
