@php
    $permission = isset($permission_name) ? $permission_name : $main;
@endphp

<x-button color="primary" dataToggle="modal" dataTarget="#purchase-request-detail-{{ $row->id }}" icon="eye" fontawesome size="sm" />

<x-modal title="Data Purchase Request" id="purchase-request-detail-{{ $row->id }}" headerColor="primary">
    <x-slot name="modal_body">
        <div class="row">
            {{-- <div class="col-md-12 mb-3">
                <ul class="list-group">
                    @foreach ($row->purchaseOrderGeneralDetails()->where('type', 'main')->get() as $item)
                        <li class="list-group-item">{{ $item->purchase_request?->kode }}</li>
                    @endforeach
                </ul>
            </div> --}}
            <div class="col-md-12 mb-3">
                @foreach ($row->purchaseOrderGeneralDetails as $detail)
                    @if ($detail->purchase_request)
                        <p>{{ $detail->purchase_request->kode }}</p>
                    @else
                        <p>Additional</p>
                    @endif
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Item</th>
                                <th>Qty</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($detail->purchase_order_general_detail_items as $detail_item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $detail_item->item->nama }}</td>
                                    <td>{{ number_format($detail_item->quantity, 0, ',', '.') . ' ' . $detail_item->unit->name }} </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
        </div>
    </x-slot>
</x-modal>

@if ($btn_config['detail']['display'])
    @can("view $permission")
        <x-button color='primary' icon='eye' fontawesome size="sm" link='{{ route("admin.$main.show", $row) }}' />
    @endcan
@endif

@if($row->check_available_date)
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