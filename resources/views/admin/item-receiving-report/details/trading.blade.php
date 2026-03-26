<x-card-data-table title="{{ 'detail ' . $title }}">
    <x-slot name="header_content">

    </x-slot>
    <x-slot name="table_content">
        @include('components.validate-error')

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('cabang') }}</label>
                    <p class="m-0">{{ $model->branch?->name }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('kode') }}</label>
                    <p class="m-0">{{ $model->kode }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('reference') }}</label>
                    <p class="m-0">
                        <a href="{{ route('admin.purchase-order.show', $model->reference_id) }}" target="_blank" rel="noopener noreferrer">{{ $model->reference?->nomor_po }}</a>
                    </p>
                </div>
            </div>
            @php
                $data_sh_number = $model->reference->sale_order ? $model->reference->sale_order->sh_number : $model->reference->sh_number;
            @endphp
            <div class="col-md-12"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">SH No.</label>
                    <p id="sh_number">{{ $data_sh_number->kode ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Supply Point</label>
                    <p id="supply_point">{{ $data_sh_number->sh_number_details[0]->alamat ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">Drop Point</label>
                    <p id="drop_point">{{ $data_sh_number->sh_number_details[1]->alamat ?? '-' }}</p>
                </div>
            </div>
            <div class="col-md-12"></div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('diterima') }}</label>
                    <p class="m-0">{{ localDate($model->date_receive) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('gudang') }}</label>
                    <p class="m-0">{{ $model->ware_house?->nama }}</p>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('item') }}</label>
                    <p>{{ $model->item_receiving_report_po_trading?->item?->nama }}</p>
                    <a href="{{ route('admin.item.show', $model->item_receiving_report_po_trading?->item?->id) }}" target="_blank" rel="noopener noreferrer">{{ $model->item_receiving_report_po_trading?->item?->kode }}</a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('liter_15') }}</label>
                    <p>{{ formatNumber($model->item_receiving_report_po_trading?->liter_15) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('liter_obs') }}</label>
                    <p>{{ formatNumber($model->item_receiving_report_po_trading?->liter_obs) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('loading_order') }}</label>
                    <p>{{ $model->item_receiving_report_po_trading?->loading_order }}</p>
                </div>
            </div>
            <div class="col-md-2">
                <label for="">File</label>
                <br>
                @if ($model->file)
                    <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" icon="file" label="show file" fontawesome target="_blank" />
                @else
                    <x-button badge color="danger" size="sm" icon="eye-slash" label="no file" fontawesome />
                @endif
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="">{{ Str::headline('status') }}</label>
                    <p>
                    <div class="d-flex gap-3">
                        <div class="badge badge-lg badge-{{ item_report_status()[$model?->status]['color'] }}">
                            {{ item_report_status()[$model?->status]['label'] }} -
                            {{ item_report_status()[$model?->status]['text'] }}
                        </div>
                        @php
                            $type = $model?->tipe;

                            if ($type == 'jasa') {
                                $type = 'item-receiving-report-service';
                            } elseif ($type == 'general') {
                                $type = 'item-receiving-report-general';
                            } elseif ($type == 'trading') {
                                $type = 'item-receiving-report-trading';
                            }
                        @endphp
                    </div>
                    </p>
                </div>
            </div>
        </div>
        {{-- <x-table>
            <x-slot name="table_head">
                <th>{{ Str::headline('Sub Total') }}</th>
                <th>{{ Str::headline('Nilai Pajak') }}</th>
                <th>{{ Str::headline('Total') }}</th>
            </x-slot>
            <x-slot name="table_body">
                <td>{{ formatNumber($model->item_receiving_report_po_trading->sub_total) }}</td>
                <td>{{ formatNumber($model->item_receiving_report_po_trading->tax_total) }}</td>
                <td>{{ formatNumber($model->item_receiving_report_po_trading->total) }}</td>
            </x-slot>
        </x-table> --}}
    </x-slot>

    <x-slot name="footer">
        <div class="d-flex justify-content-end gap-1">
            {!! $auth_revert_void_button !!}
            @role('super_admin')
                @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date_receive))
                    @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'item-receiving-report'])
                @endif
            @endrole
            <x-button type="button" color='primary' fontawesome icon="history" label="riwayat transaksi" class="w-auto" size="sm" id="history-button" />
            <x-modal title="riwayat transaksi" id="history-modal" headerColor="success">
                <x-slot name="modal_body">
                    @csrf
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Transaksi</th>
                                    <th>Nomor</th>
                                </tr>
                            </thead>
                            <tbody id="history-list">

                            </tbody>
                        </table>
                    </div>
                    <div class="mt-10 border-top pt-10">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                    </div>
                </x-slot>
            </x-modal>
            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link="{{ route('admin.item-receiving-report.index') }}" />

            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                @can("edit $main")
                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main-trading.edit", $model) }}' />
                @endcan
            @endif

            <x-button color='info' fontawesome icon="file-export" class="w-auto" size="sm" link='{{ route("$main-trading.export-pdf", ['id' => encryptId($model->id)]) }}' onclick="show_print_out_modal(event)" />
        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="{{ 'additional item ' . $title }}">

    <x-slot name="table_content">
        <x-table>
            <x-slot name="table_head">
                <th>#</th>
                <th>{{ Str::headline('Item') }}</th>
                {{-- <th>{{ Str::headline('Harga') }}</th> --}}
                <th>{{ Str::headline('Diterima') }}</th>
                {{-- <th>{{ Str::headline('Sub total') }}</th> --}}
                {{-- <th>{{ Str::headline('Nilai Pajak') }}</th> --}}
                {{-- <th>{{ Str::headline('Total') }}</th> --}}
            </x-slot>
            <x-slot name="table_body">
                @foreach ($model?->item_receiving_po_trading_additionals as $additional)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>
                            <p class="mb-0 fw-bold">
                                {{ $additional->purchase_order_additional_items->item?->nama }}
                            </p>
                            <p class="mb-0">
                                <a href="{{ route('admin.item.show', $additional->purchase_order_additional_items->item?->id) }}" target="_blank" rel="noopener noreferrer">{{ $additional->purchase_order_additional_items->item?->kode }}</a>
                            </p>
                        </td>
                        {{-- <td>{{ formatNumber($additional->purchase_order_additional_items->harga ?? 0) }}</td> --}}
                        <td>{{ formatNumber($additional->receive_qty) }}</td>
                        {{-- <td>{{ formatNumber($additional->subtotal) }}</td> --}}
                        {{-- <td>{{ formatNumber($additional->tax_total) }}</td> --}}
                        {{-- <td>{{ formatNumber($additional->total) }}</td> --}}
                    </tr>
                @endforeach
            </x-slot>
        </x-table>
    </x-slot>
</x-card-data-table>
