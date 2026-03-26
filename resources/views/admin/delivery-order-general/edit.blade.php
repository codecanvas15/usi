@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order-general';
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
                        <a href="{{ route('admin.delivery-order.index') }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.delivery-order-general.show', $model) }}">{{ Str::headline("Detail $main") }}</a>
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
    <form action="{{ route("admin.$main.update", $model) }}" method="post" id="form-update">
        @csrf
        @method('PUT')

        <x-card-data-table title="{{ 'konfirmasi delivery order' }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="name" label="kode" value="{{ $model->code }}" id="" required readonly />
                        </div>
                    </div>
                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="" label="kode sale order" value="{{ $model->sale_order_general?->kode }}" id="" required readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="" label="dibuat pada" value="{{ toDayDateTimeString($model->sale_order_general?->created_at) }}" id="" required readonly />
                        </div>
                    </div>
                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-4">
                        <div class="form-group">
                            @if ($model->status != 'approve')
                                <x-input class="datepicker-input" name="date" label="tanggal" value="{{ localDate($model->date) }}" id="date" onchange="checkClosingPeriod($(this))" required />
                            @else
                                <x-input class="datepicker-input" name="date" label="tanggal" value="{{ localDate($model->date) }}" id="date" onchange="checkClosingPeriod($(this))" required readonly />
                            @endif
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            @if ($model->status != 'approve')
                                <x-input class="datepicker-input" name="target_delivery" label="target pengiriman" value="{{ localDate($model->target_delivery) }}" id="" required />
                            @else
                                <x-input class="datepicker-input" name="target_delivery" label="target pengiriman" value="{{ localDate($model->target_delivery) }}" id="" required readonly />
                            @endif
                        </div>
                    </div>
                </div>
                <div class="row mt-20 pt-20 border-top">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="date_send" label="tanggal pengiriman" value="{{ localDate($model->date_send) }}" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="date_receive" label="tanggal diterima" value="{{ localDate($model->date_receive) }}" id="" />
                        </div>
                    </div>
                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-4">
                        <div class="form-group">
                            @if ($model->status != 'approve')
                                <x-input type="text" name="drop" label="drop / ship to" value="{{ $model->drop }}" id="" required />
                            @else
                                <x-input type="text" name="drop" label="drop / ship to" value="{{ $model->drop }}" id="" required readonly />
                            @endif
                        </div>
                    </div>
                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="ware_house_id" label="dari_gudang" id="select-warehouse">
                                @if ($model->ware_house)
                                    <option value="{{ $model->ware_house?->id }}" selected>{{ $model->ware_house?->nama }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>
                </div>

                <div class="my-20 py-20 border-top border-bottom border-danger">
                    @foreach ($model->delivery_order_general_details as $item)
                        <input type="hidden" name="delivery_order_general_detail_id[]" value="{{ $item->id }}">
                        <div class="border-bottom border-primary py-20">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="item_name[]" label="item" value="{{ $item->item?->nama }} - {{ $item->item?->kode }}" id="" required readonly />
                                        <input type="hidden" name="item_id[]" value="{{ $item->item_id }}" id="detail-item-id-{{ $item->id }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" label="quantity dipesan" value="{{ formatNumber($item->sale_order_general_detail->amount) }} / {{ formatNumber($item->sale_order_general_detail->sended - $item->quantity) }}" id="" class="commas-form" helpers="dipesan / sudah dikirim - {{ $item->unit?->name }}" required readonly />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="sisa" label="sisa_stock di gudang" id="stock-sisa-{{ $item->id }}" readonly />
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" name="quantity[]" label="jumlah_dikirim" value="{{ formatNumber($item->quantity) }}" id="quantity-{{ $item->id }}" readonly required helpers="{{ $item->unit?->name }}" />
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-text-area name="detail_description[]" label="keterangan" id="" cols="30" rows="10">{!! $item->description !!}</x-text-area>
                                    </div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-text-area name="description" id="" cols="30" rows="10">{{ $model->description }}</x-text-area>
                    </div>
                </div>

            </x-slot>
            <x-slot name="footer">
                <x-button color='primary' label="Save data" iconRight fontawesome icon="edit" class="w-auto" size="sm" />
            </x-slot>
        </x-card-data-table>
    </form>

    @if (in_array($model->status, ['approve', 'done']))
        <form action="{{ route("admin.$main.close", ['id' => $model->id]) }}" method="post">
            @csrf
            @method('PUT')
            <x-card-data-table title="{{ 'konfirmasi jumlah pengiriman' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="my-20 py-20 border-top border-bottom border-danger">
                        @foreach ($model->delivery_order_general_details as $item)
                            <input type="hidden" name="delivery_order_general_detail_id[]" value="{{ $item->id }}">
                            <div class="border-bottom border-primary py-20">

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="item_name[]" label="item" value="{{ $item->item?->nama }} - {{ $item->item?->kode }}" id="" required readonly />
                                            <input type="hidden" name="item_id[]" value="{{ $item->item_id }}" id="detail-item-id-{{ $item->id }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" label="quantity dipesan" value="{{ formatNumber($item->sale_order_general_detail->amount) }} / {{ formatNumber($item->sale_order_general_detail->sended - $item->quantity) }}" id="" class="commas-form" helpers="dipesan / sudah dikirim - {{ $item->unit?->name }}" required readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="sisa" label="sisa_stock di gudang" id="stock-sisa-{{ $item->id }}" readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity[]" label="jumlah_dikirim" value="{{ formatNumber($item->quantity) }}" id="quantity-{{ $item->id }}" readonly required helpers="{{ $item->unit?->name }}" />
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity_received[]" label="jumlah diterima" value="{{ formatNumber($item->quantity_received) }}" id="quantity-received-{{ $item->id }}" class="commas-form" helpers="{{ $item->unit?->name }}" onkeyup="validateQty('{{ $item->id }}', $(this))" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity_lost[]" label="jumlah hilang" value="{{ formatNumber($item->quantity_lost) }}" id="quantity-lost-{{ $item->id }}" class="commas-form" helpers="{{ $item->unit?->name }}" onkeyup="validateQty('{{ $item->id }}', $(this))" />
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <x-input type="text" name="quantity_damage[]" label="jumlah rusak" value="{{ formatNumber($item->quantity_damage) }}" id="quantity-damage-{{ $item->id }}" class="commas-form" helpers="{{ $item->unit?->name }}" onkeyup="validateQty('{{ $item->id }}', $(this))" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </x-slot>
                <x-slot name="footer">
                    @can('edit ' . $main)
                        @if ($model->check_available_date)
                            <x-button color='primary' label="Save data" iconRight fontawesome icon="edit" class="w-auto" size="sm" />
                        @endif
                    @endcan
                </x-slot>
            </x-card-data-table>
        </form>
    @endif
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {
            let quantity_received = [],
                quantity_lost = [],
                quantity_damage = [];

            let stocks = [];

            checkClosingPeriod($('#date'));
            initSelect2Search('select-warehouse', "{{ route('admin.select.ware-house.type') }}/general", {
                id: "id",
                text: "nama",
            });

            @if ($model->warehouse_id)
                $('#select-warehouse').val({{ $model->warehouse_id }}).trigger('change');
            @endif

            @if ($model->status == 'approve')
                $('#select-warehouse').select2('destroy');
            @endif

            const displayStocks = () => {
                stocks.map((data, index) => {
                    $(`#stock-sisa-${data.id}`).val(`${data.stock} / ${data.unit?.name} `);
                });
            };

            $('#select-warehouse').change(function(e) {
                e.preventDefault();

                $.ajax({
                    type: "get",
                    url: `{{ route('admin.delivery-order-general.check-stock') }}/${this.value}/{{ $model->id }}`,
                    success: function({
                        data
                    }) {
                        stocks = data;
                        displayStocks();
                    }
                });
            });
            $.ajax({
                type: "get",
                url: `{{ route('admin.delivery-order-general.check-stock') }}/${$('#select-warehouse').val()}/{{ $model->id }}`,
                success: function({
                    data
                }) {
                    stocks = data;
                    displayStocks();
                }
            });

            $('#form-update').submit(function(e) {
                e.preventDefault();

                let result = false;

                if (stocks.length <= 0) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Stock Item kosong',
                    });

                    result = true;
                }

                if (result) {
                    $('form').each(function() {
                        $(this).find('input[type=submit]').prop('disabled', false);
                        $(this).find('button[type=submit]').prop('disabled', false);
                    });
                    return;
                }

                $(this).unbind('submit').submit();
            })
        });

        const validateQty = (id, element) => {
            let qty = thousandToFloat($(`#quantity-${id}`).val());
            let received = thousandToFloat($(`#quantity-received-${id}`).val());
            let damage = thousandToFloat($(`#quantity-damage-${id}`).val());
            let lost = thousandToFloat($(`#quantity-lost-${id}`).val());

            let total = received + damage + lost;
            if (total > qty) {
                showAlert(
                    "Peringatan",
                    "Jumlah yang diterima, rusak dan hilang melebihi jumlah yang dikirim",
                    "warning"
                );

                element.val(0);
            }
        }

        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')
    </script>
@endsection
