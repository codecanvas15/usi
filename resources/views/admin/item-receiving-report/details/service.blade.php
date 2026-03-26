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
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">{{ Str::headline('kode') }}</label>
                    <p class="m-0">{{ $model->kode }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">{{ Str::headline('vendor') }}</label>
                    <p class="m-0">{{ $model->vendor->nama }}</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">{{ Str::headline('reference') }}</label>
                    <p class="m-0">
                        <a href="{{ route('admin.purchase-order-service.show', $model->reference_id) }}" target="_blank" rel="noopener noreferrer">{{ $model->reference->spk_number ? $model->reference->spk_number . ' / ' : '' }} {{ $model->reference->code }}</a>
                    </p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label for="">{{ Str::headline('diterima') }}</label>
                    <p class="m-0">{{ localDate($model->date_receive) }}</p>
                </div>
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
            @if ($projects)
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="">{{ Str::headline('project') }}</label>
                        <br>
                        @foreach ($projects ?? [] as $project)
                            <a href="{{ route('admin.project.show', $project) }}">{{ $project->name }}</a>
                            @if (!$loop->last)
                                ,
                            @endif
                        @endforeach
                    </div>
                </div>
            @endif
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
                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main-service.edit", $model) }}' />
                @endcan
            @endif

            <x-button color='info' fontawesome icon="file-export" class="w-auto" size="sm" link='{{ route("$main-service.export-pdf", ['id' => encryptId($model->id)]) }}' onclick="show_print_out_modal(event)" />
        </div>
    </x-slot>
</x-card-data-table>

<x-card-data-table title="{{ 'detail item ' . $title }}">

    <x-slot name="table_content">
        <x-table>
            <x-slot name="table_head">
                <th>#</th>
                <th>{{ Str::headline('Item') }}</th>
                <th>{{ Str::headline('Qty Dipesan') }}</th>
                <th>{{ Str::headline('Qty Diterima') }}</th>
                {{-- <th>{{ Str::headline('Harga') }}</th>
                <th>{{ Str::headline('Sub total') }}</th>
                <th>{{ Str::headline('Nilai Pajak') }}</th>
                <th>{{ Str::headline('Total') }}</th> --}}
            </x-slot>
            <x-slot name="table_body">
                @foreach ($model?->item_receiving_report_details as $item)
                    <tr>
                        <td>{{ $loop->index + 1 }}</td>
                        <td>
                            <p class="mb-0 fw-bold">
                                {{ $item->reference?->item?->nama }}
                            </p>
                            <p class="mb-0">
                                <a href="{{ route('admin.item.show', $item->reference?->item?->id) }}" target="_blank" rel="noopener noreferrer">{{ $item->reference?->item?->kode }}</a>
                            </p>
                        </td>
                        <td>{{ formatNumber($item->reference?->quantity) }} {{ $item->reference?->item?->unit?->name }}</td>
                        <td>{{ formatNumber($item->jumlah_diterima) }} {{ $item->reference?->item?->unit?->name }}</td>
                        {{-- <td>{{ formatNumber($item->reference->price) }}</td>
                        <td>{{ formatNumber($item->sub_total) }}</td>
                        <td>{{ formatNumber($item->tax_total) }}</td>
                        <td>{{ formatNumber($item->total) }}</td> --}}
                    </tr>
                @endforeach
            </x-slot>

            {{-- <x-slot name="table_foot">
                <tr>
                    <th colspan="5" class="text-end">Total</th>
                    <th>{{ formatNumber($model->sub_total) }}</th>
                    <th>{{ formatNumber($model->tax_total) }}</th>
                    <th>{{ formatNumber($model->total) }}</th>
                </tr>
            </x-slot> --}}
        </x-table>
    </x-slot>
</x-card-data-table>
