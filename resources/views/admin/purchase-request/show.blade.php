@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @canany(['view purchase-request-service', 'view purchase-request-general', 'view purchase-request-transport'])
        <div>
            <div class="box bg-gradient-warning-dark text-white">
                <div class="box-body">
                    <div class="row justify-content-end">
                        <div class="col-md-6 align-self-center">
                            <h4 class="m-0">Detail Purchase Request</h4>
                            <h1 class="m-0">{{ $model->kode }}</h1>
                        </div>
                        <div class="col-md-6 align-self-center">
                            <div class="row justify-content-end">
                                <div class="col-md-3 d-flex flex-column">
                                    <h5 class="text-center">{{ Str::headline('status_purchase_request') }}</h5>
                                    <div class="badge badge-lg badge-{{ purchase_request_status()[$model->status]['color'] }}">
                                        {{ Str::headline(purchase_request_status()[$model->status]['label']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->kode }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('project') }}</label>
                                    <p>{{ $model->project?->name }} / {{ $model->project?->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->tanggal) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('type') }}</label>
                                    <p>{{ $model->type }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('divisi') }}</label>
                                    <p>{{ $model->division?->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for=""> {{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ purchase_request_status()[$model->status]['color'] }}">
                                        {{ purchase_request_status()[$model->status]['label'] }} -
                                        {{ purchase_request_status()[$model->status]['text'] }}
                                    </div>

                                    @php
                                        $type = $model->type;

                                        if ($type == 'jasa') {
                                            $type = 'purchase-request-service';
                                        } elseif ($type == 'general') {
                                            $type = 'purchase-request-general';
                                        } elseif ($type == 'transportir') {
                                            $type = 'purchase-request-transport';
                                        }
                                    @endphp
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('keterangan') }}</label>
                                    <p>{!! $model->keterangan !!}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('created_by') }}</label>
                                    <p>{{ $model->created_by_user->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('created_at') }}</label>
                                    <p>{{ toDayDateTimeString($model->created_at) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('last medified') }}</label>
                                    <p>{{ toDayDateTimeString($model->updated_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
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

                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->check_available_date)
                                {!! $auth_revert_void_button !!}

                                @if ($model->status == 'pending' or $model->status == 'revert')
                                    @can("edit $type")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan

                                    @can("delete $type")
                                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="{{ $main . ' item' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Item</th>
                                <th>Jumlah</th>
                                <th>Jumlah di approve</th>
                                <th>Unit</th>
                                <th>Alasan Reject</th>
                                <th>Attachment</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->purchase_request_details as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->item_id ? $item->item_data->kode . ' ' . $item->item_data->nama : $item->item }}
                                        </td>
                                        <td>{{ formatNUmber($item->jumlah) }}</td>
                                        <td>
                                            @if ($can_approve)
                                                <div class="form-group">
                                                    <input type="text" class="form-control commas-form text-end" name="jumlah_diapprove[]" value="{{ formatNumber($item->jumlah_diapprove > 0 ? $item->jumlah_diapprove : $item->jumlah) }}" required />
                                                    <input type="hidden" name="purchase_request_detail[]" value="{{ $item->id }}" required />
                                                </div>
                                            @else
                                                {{ formatNUmber($item->jumlah_diapprove) }}
                                            @endif
                                        </td>
                                        <td>{{ $item->unit?->name ?? '-' }}</td>
                                        <td>{{ $item->reject_reason ?? '-' }}</td>
                                        <td>
                                            @if ($item->file)
                                                <x-button color="primary" icon="eye" fontawesome link="{{ asset('storage/' . $item->file) }}" size="sm" />
                                            @else
                                                <x-button color="danger" badge icon="eye-slash" fontawesome size="sm" label="not available" />
                                            @endif
                                        </td>
                                        <td>
                                            <x-button color="primary" dataToggle="modal" dataTarget="#detail-modal-{{ $item->id }}" icon="align-left" fontawesome size="sm" />
                                            <x-modal title="keterangan item" id="detail-modal-{{ $item->id }}">
                                                <x-slot name="modal_body">
                                                    <h4>keterangan</h4>
                                                    <p>{{ $item->keterangan }}</p>
                                                </x-slot>
                                                <x-slot name="modal_footer">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="close" />
                                                </x-slot>
                                            </x-modal>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="Lock stock">

                    <x-slot name="header_content">
                        @if (in_array($model->status, ['approve', 'partial-approve', 'partial-reject']) && !$model->if_purchase_request_detail_all_from_master_item)
                            <x-button color="info" icon="plus" dataToggle="modal" id="add-lock-stock" fontawesome label="Tambah" />
                        @endif

                        <form action="{{ route('admin.purchase-request.lock-stock', ['id' => $model->id]) }}" method="post" id="form-lock-stock" class="my-20">
                            @csrf

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-select name="purchase_request_detail_id" required>
                                            <option value="">-- pilih item --</option>
                                            @foreach ($model->purchase_request_details as $item)
                                                <option value="{{ $item->id }}">{{ $item->item_data->nama }} -
                                                    {{ $item->item_data->kode }}</option>
                                            @endforeach
                                        </x-select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="quantity" class="commas-form" required />
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-self-end">
                                    <div class="form-group">
                                        <x-button type="button" color="secondary" size="sm" id="cancel-lock-stock" label="cancel" />
                                        <x-button type="submit" color="primary" size="sm" label="Save" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </x-slot>

                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Item</th>
                                <th>Lock</th>
                                <th>Status</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->purchase_request_lock_stocks_data as $item)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $item->item->nama }} - {{ $item->item->kode }}</td>
                                        <td>{{ formatNumber($item->quantity) }}</td>
                                        <td>{{ Str::headline($item->status) }}</td>
                                        <td>
                                            <x-button color="danger" label="Delete" dataTarget="#delete-lock-stock-{{ $item->id }}" dataToggle="modal" />
                                            <x-modal-delete id="delete-lock-stock-{{ $item->id }}" url="admin.purchase-request.unlock-stock" :dataId="$item->purchase_request_detail_id" />
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>

            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <div id="print-request-container"></div>
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        {{-- ================================= APPROVE | PARTIAL APPROVE | PARTIAL REJECT | REJECT | REVERT  ======================================== --}}
                        @if ($model->check_available_date)
                            @if (in_array($model->status, ['approve', 'partial']))
                                @can("close $type")
                                    <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                    <x-modal title="approve purchase request" id="close-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="done">
                                                <div class="row">
                                                    <div class="col-12">
                                                        <div class="form-group">
                                                            <label for="closeNotes" class="form-label">Keterangan</label>
                                                            <textarea name="close_notes" id="closeNotes" rows="3" required></textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif

                    </x-slot>
                </x-card-data-table>

                <x-card-data-table>
                    <x-slot name="table_content">
                        <button type="button" class="btn btn-info" target="_blank" href="{{ route($main . '.export.id', ['id' => encryptId($model->id)]) }}" onclick="show_print_out_modal(event)" @authorize_print('purchase_request_' . $model->type) data-model="{{ \App\Models\PurchaseRequest::class }}" data-id="{{ $model->id }}" data-print-type="purchase_request_{{ $model->type }}" data-link="{{ route('admin.purchase-request.index') . '/' . $model->id }}" data-code="{{ $model->kode }}" @endauthorize_print><i class="fa fa-file"></i> Export</button>
                    </x-slot>
                </x-card-data-table>

                @can("approve $type")
                    @if (in_array($model->status, ['pending', 'revert', 'approve']))
                        <x-card-data-table title="{{ 'Check Stock' }}">
                            <x-slot name="header_content">

                            </x-slot>
                            <x-slot name="table_content">
                                <div class="form-group">
                                    <x-select name="" label="item" id="item-select" required></x-select>
                                </div>
                                <div class="form-group">
                                    <x-input type="text" name="stock" label="Stock" id="stock-warehouse" readonly />
                                </div>
                                <div class="form-group">
                                    <x-button class="col-md-12 btn-check-stock" color="primary" label="Check Stock" dataToggle="modal" dataTarget="#check-stock-modal" />
                                </div>
                            </x-slot>
                        </x-card-data-table>
                    @endif
                @endcan

                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-modal title="Cek Stok" id="check-stock-modal" headerColor="primary">
                    <x-slot name="modal_body">
                        <x-table theadColor='danger' id="warehouse-stock-detail">
                            <x-slot name="table_head">
                                <th>Warehouse</th>
                                <th>Qty</th>
                            </x-slot>
                            <x-slot name="table_body"></x-slot>
                        </x-table>
                    </x-slot>
                    <x-slot name="modal_footer">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="Tutup" />
                    </x-slot>
                </x-modal>
            </div>
        </div>
    @endcanany
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase-request');
        $('body').addClass('sidebar-collapse');
    </script>

    @canany(['approve purchase-request-service', 'approve purchase-request-general', 'approve purchase-request-transport'])
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script>
            $(document).ready(function() {
                inititemSelect('item-select')

                $('#warehouse-select').select2('close');
                $('#warehouse-select').html('');
                $('#warehouse-select').val(null);
                $('#item-select').change(function(e) {
                    e.preventDefault();

                    $.ajax({
                        url: `{{ route('admin.purchase-request.get-warehouse') }}/${this.value}`,
                        success: function({
                            data
                        }) {
                            var stocks = [];
                            $('#warehouse-stock-detail tbody').html('');

                            let {
                                warehouse,
                                lock_stock
                            } = data;

                            if (warehouse.length > 0) {
                                $('.btn-check-stock').text('Check Stock');
                                $('.btn-check-stock').attr('disabled', false);

                                $.each(warehouse, function(key, value) {
                                    stocks.push(parseInt(decimalFormatter(value.stock)));
                                    const html = `<tr>
                                        <td><a href="/ware-house/${value.id}" class="text-primary text-decoration-underline hover_text-dark">${value.nama}</a></td>
                                        <td class="text-start">${decimalFormatter(value.stock)}</td>
                                    </tr>`;
                                    $('#warehouse-stock-detail tbody').append(html);
                                });

                            } else {

                                $('#stock-warehouse').val(0);
                                $('.btn-check-stock').text('Tidak Ada Stock');
                                $('.btn-check-stock').attr('disabled', 'disabled');

                            }

                            var stock = stocks.reduce(function(a, b) {
                                return a + b;
                            }, 0);

                            $('#warehouse-stock-detail tbody').append(`
                                <tr>
                                    <td>Total Stock</td>
                                    <td class="text-start">${decimalFormatter(stock)}</td>
                                </tr>
                            `);


                            if (lock_stock > 0) {
                                $('#warehouse-stock-detail tbody').append(`
                                    <tr>
                                        <td>Lock Stock</td>
                                        <td class="text-start">-${decimalFormatter(lock_stock)}</td>
                                    </tr>
                                `)
                            }

                            $('#warehouse-stock-detail tbody').append(`
                                <tr>
                                    <td>Total final</td>
                                    <td class="text-start">${decimalFormatter(stock - lock_stock)}</td>
                                </tr>
                            `);

                            $('#stock-warehouse').val(stock - lock_stock);
                        }
                    });

                });
            });
        </script>
        <script>
            $(document).ready(function() {
                $('#form-lock-stock').hide();

                $('#add-lock-stock').click(function(e) {
                    e.preventDefault();
                    $('#form-lock-stock').toggle();
                });

                $('#cancel-lock-stock').click(function(e) {
                    e.preventDefault();
                    $('#form-lock-stock').hide();
                });

                $('#approve-form').submit(function(e) {
                    e.preventDefault();
                    var jumlah_diapprove = $('input[name="jumlah_diapprove[]"]');
                    var total_jumlah_diapprove = 0;
                    jumlah_diapprove.each(function() {
                        $('#approve-form').append('<input type="hidden" name="jumlah_diapprove[]" value="' + $(this).val() + '">');
                        total_jumlah_diapprove += thousandToFloat($(this).val());
                    });

                    var total_jumlah = '{{ $model->purchase_request_details->sum('jumlah') }}';
                    if (parseFloat(total_jumlah_diapprove).toFixed(2) > parseFloat(total_jumlah).toFixed(2)) {
                        e.preventDefault();
                        alert('Jumlah di approve tidak boleh lebih dari jumlah');

                        setTimeout(() => {
                            $('#approve-form').find('input[name="jumlah_diapprove[]"]').remove();
                            $('#approve-form').find('input[name="purchase_request_detail[]"]').remove();

                            $('#approve-form').find('button[type="submit"]').attr('disabled', false);
                        }, 1000);
                    }

                    var purchase_request_detail = $('input[name="purchase_request_detail[]"]');
                    purchase_request_detail.each(function() {
                        $('#approve-form').append('<input type="hidden" name="purchase_request_detail[]" value="' + $(this).val() + '">');
                    });

                    setTimeout(() => {
                        $('#approve-form').unbind('submit').submit();
                    }, 1500);
                })
            });

            $('#history-button').on('click', function() {
                $.ajax({
                    url: `{{ route('admin.purchase-request.history', $model->id) }}`,
                    success: function({
                        data
                    }) {
                        $('#history-list').html('');
                        $.each(data, function(key, value) {
                            let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                            $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                        });

                        $('#history-modal').modal('show');
                    }
                });
            });

            get_request_print_approval(`App\\Models\\PurchaseRequest`, '{{ $model->id }}', 'purchase_request_{{ $model->type }}');
        </script>
    @endcan
@endsection
