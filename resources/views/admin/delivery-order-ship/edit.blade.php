@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order-ship';
    $title = Str::headline('Delivery order kapal');
@endphp

@section('title', Str::headline("Ediit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.delivery.index') }}">{{ Str::headline('delivery order') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.delivery-order-ship.update', ['delivery_order_ship' => $model]) }}" method="post" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <x-card-data-table :title='"Edit $title"'>
            <x-slot name="header_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="code" label="kode" value="{{ $model->code }}" id="" required disabled />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" value="{{ $model->branch->name }}" label="cabang" disabled required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" value="{{ $model->soTrading->nomor_so }}" label="Sale order" disabled required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" value="{{ $model->purchaseTransport->kode }}" label="purchase transport" disabled required />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" label="target pengiriman" name="target_delivery" value="{{ localDate($model->target_delivery) }}" id="" />
                        </div>
                    </div>
                </div>
                `
                <div class="row border-top border-primary mt-10 pt-10">
                    <div class="col-md-3">
                        <div class="form-group">
                            @if ($model->purchaseTransport)
                                <x-select name="ware_house_id" label='dari stock' id="warehouse-select" helpers="untuk pengurangan stock di stock card." disabled>
                                    @if ($model->ware_house_id)
                                        <option value="{{ $model->ware_house_id }}" selected>{{ $model->wareHouse->nama }}</option>
                                    @endif
                                </x-select>
                                <input type="hidden" name="ware_house_id" value="{{ $model->ware_house_id }}">
                            @else
                                <x-select name="ware_house_id" label='dari stock' id="warehouse-select" helpers="untuk pengurangan stock di stock card.">
                                    @if ($model->ware_house_id)
                                        <option value="{{ $model->ware_house_id }}" selected>{{ $model->wareHouse->nama }}</option>
                                    @endif
                                </x-select>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="" id="stock-remaining" label="sisa stock" class="commas-form" readonly />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" label="tanggal muat" name="load_date" value="{{ localDate($model->load_date) }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="load_quantity" label="realisasi kuantitas muat" id="" value="{{ formatNumber($model->load_quantity) }}" class="commas-form" disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="load_quantity_realization" label="kuantitas muat" id="" value="{{ formatNumber($model->load_quantity_realization) }}" class="commas-form" onkeyup="checkStock($(this))" />
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" label="tanggal bongkar" name="unload_date" value="{{ localDate($model->unload_date) }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="unload_quantity_realization" label="realisasi kuantitas bongkar" id="" value="{{ formatNumber($model->unload_quantity_realization) }}" class="commas-form" />
                        </div>
                    </div>
                </div>

                <div class="row border-top border-primary mt-10 pt-10">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="hpp" label="hpp" value="{{ $model->hpp }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="top_seal" label="segel atas" value="{{ $model->top_seal }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="bottom_seal" label="segel bawah" value="{{ $model->bottom_seal }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="temperature" label="temperatur" value="{{ $model->temperature }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="initial_meter" label="meter awal" value="{{ $model->initial_meter }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="initial_final" label="meter akhhir" value="{{ $model->initial_final }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="sg_meter" label="sg meter" value="{{ $model->sg_meter }}" id="" />
                        </div>
                    </div>
                </div>

                <div class="row border-top border-primary mt-10 pt-10">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="file" name="file" id="" label="file" value="{{ $model->file }}" />
                        </div>
                    </div>
                </div>

                <div class="row border-top border-primary mt-10 pt-10">
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-text-area name="description" label="deskripsi" id="" cols="30" rows="10"></x-text-area>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <x-text-area name="fleet_information" label="informasi kendaraan" id="" cols="30" rows="10"></x-text-area>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" required />
                    <x-button type="submit" color="primary" label="Save data" required />
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/vehicleSelect.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

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

                $.ajax({
                    type: "POST",
                    url: "{{ route('admin.purchase-order-transport.check-stock') }}",
                    data: {
                        ware_house_id: $(this).val(),
                        item_id: "{{ $model->soTrading?->so_trading_detail?->item_id }}",
                        _token: token,
                    },
                    success: function({
                        data
                    }) {
                        stock_left = data.stock;
                        stock_left = parseFloat(stock) + parseFloat("{{ $model->load_quantity_realization }}");;
                        $('#stock-remaining').val(stock_left);
                        $('#stock-remaining').trigger('blur');
                    }
                });

            });

            @if ($model->wareHouse)
                $('#warehouse-select').trigger('change');
            @endif

        });

        const checkStock = (e) => {
            if (thousandToFloat($(e).val()) > thousandToFloat($('#stock-remaining').val())) {
                showAlert('', 'Jumlah Stock Digudang Tidak Mencukupi!', 'warning');
                $(e).val(0);
            }
        };
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order');
    </script>
@endsection
