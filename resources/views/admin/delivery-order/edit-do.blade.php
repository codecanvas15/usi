@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                        <a href="{{ route("admin.$main.show", $model->so_trading_id) }}">{{ Str::headline("List $main") }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.' . $main . '.list-delivery-order.show', ['sale_order_id' => $model->so_trading_id, 'delivery_order_id' => $model->id]) }}">{{ Str::headline("Detail $main") }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'Edit ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <form action="{{ route('admin.delivery-order.list-delivery-order.update', ['delivery_order_id' => $model->id, 'sale_order_id' => $model->so_trading_id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                @if ($model->so_trading->so_trading_detail->item->item_category->item_type->nama == 'purchase item')
                    {{-- if from purchase transport ============================================================ --}}
                    @if (!is_null($model->purchase_transport_id))
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="purchase_transport_code" label='Purchase Transport' value="{{ $model->purchase_transport?->kode }}" id="" disabled />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="external_number" label='no do external' value="{{ $model->external_number }}" id="" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="nama driver" name="driver_name" value="{{ $model->driver_name }}" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="nomor hp driver" name="driver_phone" value="{{ $model->driver_phone }}" id="" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="informasi kendaraan" name="vehicle_information" value="{{ $model->vehicle_information }}" id="" required />
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- end if from purchase transport ============================================================ --}}

                    {{-- if not from purchase transport ============================================================== --}}
                    @if (is_null($model->purchase_transport_id))
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="fleet_id" label="kendaraan" id="fleet-select" helpers="Dikirim dengan kendaraan">
                                        @if (!is_null($model->fleet_id))
                                            <option value="{{ $model->fleet_id }}" selected>{{ $model->fleet->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="employee_id" label="driver" id="employee-select">
                                        @if ($model->employee)
                                            <option value="{{ $model->employee_id }}" selected>{{ $model->employee->name }} - {{ $model->employee->NIK }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- end if not from purchase transport ============================================================== --}}

                    {{-- select item receiving report for stock mutation out --}}
                    @if ($model->purchase_transport && !$model->delivery_order)
                        <div class="row mt-20 pt-20 border-top">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="ware_house_id" label='dari stock' id="warehouse-select">
                                        @if ($model->ware_house_id)
                                            <option value="{{ $model->ware_house_id }}" selected>{{ $model->ware_house->nama }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="" id="stock-remaining" label="sisa stock" class="commas-form" readonly />
                                </div>
                            </div>
                        </div>
                    @elseif ($model->purchase_transport && $model->delivery_order)
                        <div class="row mt-20 pt-20 border-top">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select name="delivery_order_id" label='delivery order' id="deliveryorder-select" disabled>
                                        @if ($model->delivery_order_id)
                                            <option value="{{ $model->delivery_order_id }}" selected>{{ $model->delivery_order->code }}</option>
                                        @endif
                                    </x-select>
                                    <input type="hidden" name="delivery_order_id" value="{{ $model->delivery_order_id }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="" id="stock-remaining" label="sisa" class="commas-form" readonly />
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mt-20 pt-20 border-top">
                            <div class="col-md-4">
                                <div class="form-group">

                                    <x-select name="ware_house_id" label='dari stock' id="warehouse-select">
                                        @if ($model->ware_house_id)
                                            <option value="{{ $model->ware_house_id }}" selected>{{ $model->ware_house->nama }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="" id="stock-remaining" label="sisa stock" class="commas-form" readonly />
                                </div>
                            </div>
                        </div>
                    @endif
                    {{-- select item receiving report for stock mutation out --}}

                    {{-- target delivery --}}
                    <div class="row mt-20 pt-20 border-top">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" onchange="checkClosingPeriod($(this))" name="target_delivery" label='target delivery' value="{{ localDate($model->target_delivery) }}" id="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="item_receiving_report_id" label='No. LPB' id="item_receiving_report_id">
                                    @if ($model->item_receiving_report_id)
                                        <option value="{{ $model->item_receiving_report_id }}" selected>{{ $model->item_receiving_report->kode }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>
                    {{-- target delivery --}}
                @endif

                {{-- load and unload data --}}
                <div class="mt-20 pt-20 border-top border-bottom border-primary">
                    <div class="row">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" onchange="checkClosingPeriod($(this))" name="load_date" label="tanggal_muat" id="load_date" value="{{ localDate($model->load_date ?? '') }}" />
                                </div>
                            </div>
                            @if ($model->status != 'done')
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="load_quantity" label="kapasitas muat" id="" value="{{ formatNumber($model->load_quantity ?? 0) }}" class="commas-form" helpers="{{ $unit }}" />
                                    </div>
                                </div>
                            @else
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input type="text" name="load_quantity" label="kapasitas muat" id="" value="{{ formatNumber($model->load_quantity ?? 0) }}" class="commas-form" helpers="{{ $unit }}" readonly />
                                    </div>
                                </div>
                            @endif

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="load_quantity_realization" label="kapasitas muat realisasi" id="" value="{{ formatNumber($model->load_quantity_realization) }}" helpers="{{ $unit }}" class="commas-form" onkeyup="checkStock($(this))" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" onchange="checkClosingPeriod($(this))" name="unload_date" label="tanggal_bongkar" id="unload_date" value="{{ localDate($model->unload_date ?? '') }}" />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="unload_quantity_realization" label="kapasitas bongkar realisasi" id="" value="{{ formatNumber($model->unload_quantity_realization) }}" helpers="{{ $unit }}" class="commas-form" />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- end load and unload data --}}

                {{-- details --}}
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="top_seal" value="{{ $model->top_seal }}" label="segel atas" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="bottom_seal" value="{{ $model->bottom_seal }}" label="segel bawah" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="temperature" value="{{ $model->temperature }}" label="temperatur" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="initial_meter" value="{{ $model->initial_meter }}" label="meter awal" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="initial_final" value="{{ $model->initial_final }}" label="meter akhir" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="sg_meter" value="{{ $model->sg_meter }}" label="sg_meter" id="" />
                        </div>
                    </div>
                </div>
                {{-- end details --}}

                {{-- file and description --}}
                <div class="row mt-20 border-top border-primary py-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            @if ($model->file)
                                <x-input type="file" name="file" label="file" id="" />
                                <a href="{{ asset('storage/' . $model->file) }}" target="_blank"><i class="fa fa-file"></i> Lihat file</a>
                            @else
                                <x-input type="file" name="file" label="file" id="" required />
                            @endif
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
                {{-- end file and description --}}

            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/vehicleSelect.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    @if (is_null($model->purchase_transport_id))
        <script>
            $(document).ready(function() {
                initVehicleSelect2Search(`fleet-select`, `{{ route('admin.select.fleet.type') }}/darat`);
            });
        </script>
    @endif

    <script>
        $(document).ready(function() {
            let stock_left = 0;

            initSelect2Search(`warehouse-select`, `{{ route('admin.select.ware-house.type') }}/trading`, {
                id: 'id',
                text: 'nama',
            });

            initSelectEmployee("#employee-select")

            $('#warehouse-select').change(function(e) {
                e.preventDefault();

                let warehouse = $(this).val();

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.delivery-order.check-stock', ['delivery_order_id' => $model->id, 'sale_order_id' => $model->so_trading_id]) }}",
                    data: {
                        warehouse_id: $(this).val(),
                        _token: token,
                    },
                    success: function({
                        data
                    }) {
                        let stock = data.stock;

                        stock_left = parseFloat(stock);

                        $('#stock-remaining').val(stock_left);
                        $('#stock-remaining').trigger('blur');
                    }
                });

            });

            $('#deliveryorder-select').change(function(e) {
                e.preventDefault();
                let value = $('#deliveryorder-select').val();

                $.ajax({
                    type: "get",
                    url: `{{ route('admin.delivery-order.check-stock-delivery') }}/${this.value}`,
                    success: function({
                        data
                    }) {

                        let stock = data.stock;
                        stock_left = parseFloat(stock);

                        $('#stock-remaining').val(stock_left);
                        $('#stock-remaining').trigger('blur');
                    }
                });
            });

            @if ($model->ware_house_id)
                $('#warehouse-select').trigger('change');
            @endif

            @if ($model->delivery_order_id)
                $('#deliveryorder-select').trigger('change');
            @endif

        });

        const checkStock = (e) => {
            if (thousandToFloat($(e).val()) > thousandToFloat($('#stock-remaining').val())) {
                showAlert('', 'Jumlah Stock Digudang Tidak Mencukupi!', 'warning');
                $(e).val(0);
            }
        };

        initSelect2SearchPaginationData(`item_receiving_report_id`, "{{ route('admin.select.delivery-order.item-receiving-report-select') }}", {
            id: "id",
            text: "kode,loading_order"
        }, 0, {
            id: '{{ $model->id }}'
        });

        $('#load_date').change(function() {
            validate_date();
        });

        $('#unload_date').change(function() {
            validate_date();
        });

        function validate_date() {
            let loadDateInput = parseDate($('#load_date').val());
            let loadDate = new Date(loadDateInput);

            let unloadDateInput = parseDate($('#unload_date').val());
            let unloadDate = new Date(unloadDateInput);

            if (loadDate == 'Invalid Date' || unloadDate == 'Invalid Date') {
                return;
            }

            if (unloadDate < loadDate) {
                showAlert('', 'Tanggal Bongkar tidak boleh sebelum Tanggal Muat!', 'warning');
                $('#unload_date').val('');
            }
        }
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')
    </script>
@endsection
