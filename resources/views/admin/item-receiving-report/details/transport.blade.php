<x-card-data-table title="{{ 'detail ' . $title }}">
    <x-slot name="header_content">

    </x-slot>
    <x-slot name="table_content">
        @include('components.validate-error')

        <div class="row">
            <div class="col-md-3">
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
                    <label for="">{{ Str::headline('vendor') }}</label>
                    <p class="m-0">{{ $model->vendor->nama }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('loss tolerance') }}</label>
                    <p class="m-0">{{ formatNumber($model->item_receiving_report_purchase_transport?->loss_tolerance) }}%</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('potongan losses') }}</label>
                    <p class="m-0">{{ formatNumber($model->item_receiving_report_purchase_transport?->lost_discount) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('Opsi Pajak') }}</label>
                    <p class="m-0">{{ Str::headline($model->item_receiving_report_purchase_transport?->tax_option) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('reference') }}</label>
                    <p class="m-0">
                        <a href="{{ route('admin.purchase-order-transport.show', $model->reference_id) }}" target="_blank" rel="noopener noreferrer">{{ $model->reference?->kode }}</a>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('diterima') }}</label>
                    <p class="m-0">{{ localDate($model->date_receive) }}</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="">{{ Str::headline('item') }}</label>
                    <p>{{ $model->item_receiving_report_purchase_transport?->item?->nama }} - {{ $model->item_receiving_report_purchase_transport?->item?->kode }}</p>
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
        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="{{ 'detail item ' . $title }}">

    <x-slot name="table_content">
        <x-table>
            @if ($model->reference->so_trading)
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('Delivery Order') }}</th>
                    <th>{{ Str::headline('Dikirim') }}</th>
                    <th>{{ Str::headline('Diterima') }}</th>
                    <th>{{ Str::headline('Losses') }}</th>
                </x-slot>
                <x-slot name="table_body">
                    @foreach ($model->item_receiving_report_purchase_transport?->item_receiving_report_purchase_transport_details ?? [] as $item)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->delivery_order?->code }} - {{ $item->delivery_order?->external_number }}</td>
                            <td>{{ formatNumber($item->delivery_order?->load_quantity_realization) }}</td>
                            <td>{{ formatNumber($item->delivery_order?->unload_quantity_realization) }}</td>
                            <td>{{ formatNumber($item->get_losses()->losses) }} - {{ formatNumber($item->get_losses()->losses_percentage) }}%</td>
                        </tr>
                    @endforeach
                </x-slot>
            @else
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('jumlah DO') }}</th>
                    <th>{{ Str::headline('jumlah') }}</th>
                    <th>{{ Str::headline('jenis kendaraan') }}</th>
                    <th>{{ Str::headline('informasi kendaraan') }}</th>
                </x-slot>
                <x-slot name="table_body">
                    @foreach ($model->reference->purchase_transport_details ?? [] as $item)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ formatNumber($item->jumlah_do) }}</td>
                            <td>{{ formatNumber($item->jumlah) }}</td>
                            <td>{{ Str::headline($item->vehicle_type) }}</td>
                            <td>{{ $item->vehicle_info }}</td>
                        </tr>
                    @endforeach
                </x-slot>
            @endif
        </x-table>
    </x-slot>
</x-card-data-table>
