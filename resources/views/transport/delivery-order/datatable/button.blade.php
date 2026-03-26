@if ($row->status_print == false and (!in_array($row->status, ['request-print']) and $row->status != 'approve-request-print'))
    <x-button color="success" icon="print" fontawesome label="request print" size="sm" dataToggle="modal" dataTarget="#request-print-modal-{{ $row->id }}" />
    <x-modal title="request print" id="request-print-modal-{{ $row->id }}" headerColor="success">
        <x-slot name="modal_body">
            <form action='{{ route('transport.delivery-order.show.request.print', ['purchase_transport_id' => $model, 'delivery_order_id' => $row]) }}' method="post">
                @csrf

                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
@endif

@if ($row->status_print == false and $row->status == 'approve-request-print')
    <x-button color="success" icon="print" fontawesome label="print" size="sm" dataToggle="modal" dataTarget="#print-modal-{{ $row->id }}" />
    <x-modal title="print" id="print-modal-{{ $row->id }}" headerColor="success">
        <x-slot name="modal_body">
            <form action='{{ route('transport.delivery-order.show.print', ['purchase_transport_id' => $model, 'delivery_order_id' => $row]) }}' method="post">
                @csrf

                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
@endif

@if (!in_array($row->status, ['done']))
    @if ($row->status_print)
        <x-button color='primary' label="submit" fontawesome size="sm" link="{{ route('transport.delivery-order.detail.edit', ['purchase_transport' => $model, 'id' => $row]) }}" />
    @endif
@endif
