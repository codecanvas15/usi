@php
    $permission = isset($permission_name) ? $permission_name : $main;
@endphp

<x-button color="primary" dataToggle="modal" dataTarget="#purchase-request-detail-{{ $row->id }}" icon="eye" fontawesome size="sm" />

<x-modal title="Data Purchase Request" id="purchase-request-detail-{{ $row->id }}" headerColor="primary">
    <x-slot name="modal_body">
        <ul class="list-group">
            @foreach ($row->purchaseOrderServiceDetails()->where('type', 'main')->get() as $item)
                <li class="list-group-item">{{ $item->purchase_request?->kode }}</li>
            @endforeach
        </ul>
    </x-slot>
</x-modal>

@if ($btn_config['detail']['display'])
    @can("view $permission")
        <x-button color='primary' icon='eye' fontawesome size="sm" link='{{ route("admin.$main.show", $row) }}' />
    @endcan
@endif

@if ($row->check_available_date)
    @if ($btn_config['edit']['display'])
    @can("edit $permission")
        <x-button color='warning' icon='edit' fontawesome size="sm" link='{{ route("admin.$main.edit", $row) }}' />
    @endcan
    @endif

    @if ($btn_config['delete']['display'])
    @can("delete $permission")
        <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
        <x-modal-delete id="delete-modal-{{ $row->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $row->id }}" />
    @endcan
    @endif
@endif