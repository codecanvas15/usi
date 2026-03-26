@if ($btn_config['detail']['display'] && $model->check_available_date && $model->is_can_edit_data)
    <x-button color='primary' icon='eye' fontawesome size="sm" link="{{ route('admin.' . $main . '.list-delivery-order.show', ['sale_order_id' => $row->so_trading_id, 'id' => $row]) }}" />
@endif

@if ($btn_config['edit']['display'] && $model->check_available_date && $model->is_can_edit_data)
    <x-button color='warning' icon='edit' fontawesome size="sm" link="{{ route('admin.' . $main . '.list-delivery-order.edit', ['sale_order_id' => $row->so_trading_id, 'delivery_order_id' => $row]) }}" />
@endif

@if ($btn_config['delete']['display'] && $model->check_available_date && $model->is_can_edit_data)
    <x-button color='danger' icon='trash' fontawesome size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $row->id }}' />
    <x-modal-delete id="delete-modal-{{ $row->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $row->id }}" />
@endif

@if ($row->request_print == 'request-print' && $model->check_available_date && $model->is_can_edit_data)
    <x-button color="success" icon="print" fontawesome label="approve print" size="sm" dataToggle="modal" dataTarget="#print-modal-approve-{{ $row->id }}" />
    <x-modal title="approve request print" id="print-modal-approve-{{ $row->id }}" headerColor="success">
        <x-slot name="modal_body">
            <form action='{{ route('admin.delivery-order.approve-print-request', ['sale_order_id' => $model, 'id' => $row]) }}' method="post">
                @csrf

                <input type="hidden" name="status" value="approve">
                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>

    <x-button color="danger" icon="print" fontawesome label="reject print" size="sm" dataToggle="modal" dataTarget="#print-modal-reject-{{ $row->id }}" />
    <x-modal title="reject request print" id="print-modal-reject-{{ $row->id }}" headerColor="danger">
        <x-slot name="modal_body">
            <form action='{{ route('admin.delivery-order.approve-print-request', ['sale_order_id' => $model, 'id' => $row]) }}' method="post">
                @csrf

                <input type="hidden" name="status" value="reject">
                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
@endif
