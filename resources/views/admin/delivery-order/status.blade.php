<div class="badge badge-lg badge-{{ get_delivery_order_status()[$row->status]['color'] }}">
    {{ get_delivery_order_status()[$row->status]['label'] }} - {{ get_delivery_order_status()[$row->status]['text'] }}
</div>

<div class="ms-10 badge badge-lg badge-{{ $row->status_print ? 'success' : 'warning' }}">
    {{ $row->status_print ? 'Sudah Dicetak' : 'Belum Dicetak' }}
</div>

@if ($row->status == 'request-print')
    {{-- @can('approve delivery-order') --}}
    <x-button color="success" icon="check" fontawesome label="approve-request-print" size="sm" dataToggle="modal" dataTarget="#approve-request-print-modal" />
    <x-modal title="approve-request-print purchase order" id="approve-request-print-modal" headerColor="success">
        <x-slot name="modal_body">
            <form action='{{ route('admin.delivery-order.approve-print-request', ['sale_order_id' => $row->so_trading, 'id' => $row]) }}' method="post">
                @csrf
                <input type="hidden" name="status" value="approve-request-print">

                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
    {{-- @endcan
    @can('reject delivery-order') --}}
    <x-button color="danger" icon="print" fontawesome label="reject print" size="sm" dataToggle="modal" dataTarget="#print-modal-reject-{{ $row->id }}" />
    <x-modal title="reject request print" id="print-modal-reject-{{ $row->id }}" headerColor="danger">
        <x-slot name="modal_body">
            <form action='{{ route('admin.delivery-order.approve-print-request', ['sale_order_id' => $row->so_trading, 'id' => $row]) }}' method="post">
                @csrf

                <input type="hidden" name="status" value="reject-request-print">
                <div class="mt-10 border-top pt-10">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                </div>
            </form>
        </x-slot>
    </x-modal>
@endif

@can('approve delivery-order')
    @if ($row->status == 'submitted')
        <x-button color="success" icon="check" fontawesome label="approve submitted" size="sm" dataToggle="modal" dataTarget="#approve-submitted-modal" />
        <x-modal title="approve-submitted purchase order" id="approve-submitted-modal" headerColor="warning">
            <x-slot name="modal_body">
                <form action='{{ route('admin.delivery-order.approve-status-detail', ['id' => $row->so_trading_id, 'delivery_order_id' => $row]) }}' method="post">
                    @csrf
                    <div class="mt-10 border-top pt-10">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                        <x-button type="submit" color="primary" label="approve" size="sm" icon="save" fontawesome />
                    </div>
                </form>
            </x-slot>
        </x-modal>

        <x-button color="dark" icon="x" fontawesome label="reject submitted" size="sm" dataToggle="modal" dataTarget="#reject-submitted-modal" />
        <x-modal title="reject-submitted purchase order" id="reject-submitted-modal" headerColor="warning">
            <x-slot name="modal_body">
                <form action='{{ route('admin.delivery-order.reject-status-detail', ['id' => $row->so_trading_id, 'delivery_order_id' => $row]) }}' method="post">
                    @csrf
                    <div class="mt-10 border-top pt-10">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                        <x-button type="submit" color="primary" label="reject" size="sm" icon="save" fontawesome />
                    </div>
                </form>
            </x-slot>
        </x-modal>
    @endif
@endcan
