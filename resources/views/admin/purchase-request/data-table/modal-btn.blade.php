
@if ($data->check_available_date)
    @if (in_array($data->status, ['pending', 'revert']))
        <x-button color="warning" size="sm" link="{{ route('admin.purchase-request.edit', $data->id) }}" icon="edit" fontawesome />
        <x-button color="danger" size="sm" dataToggle="modal" dataTarget="#modal-pr-delete{{ $data->id }}" icon="trash" fontawesome />
        <x-modal-delete id="modal-pr-delete{{ $data->id }}" url='{{ "admin.purchase-request.destroy" }}' dataId="{{ $data->id }}" />
    @endif
@endif
<x-button color="primary" size="sm" dataToggle="modal" dataTarget="#modal-purchase-request-{{ $data->id }}" icon="eye" fontawesome />
<x-modal headerColor="primary" title="Purchase Request Detail" id="modal-purchase-request-{{ $data->id }}" modalSize="1000">
    <x-slot name="modal_body">
        <div class="row">
            @if ($data->type == 'general')
                @include('admin.purchase-request.data-table.data.general')
            @endif
            @if ($data->type == 'jasa')
                @include('admin.purchase-request.data-table.data.service')
            @endif
        </div>
    </x-slot>
</x-modal>


