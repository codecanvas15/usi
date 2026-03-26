@extends('layouts.admin.layout.index')

@php
    $main = 'stock-transfer';
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
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('edit ' . $main)
        <x-card-data-table title="{{ 'create ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <form action="{{ route("admin.$main.update", $model) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="date" name="date" label="Tanggal" value="{{ localDate($model->date) }}" required autofucus onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="from" id="from" label="Gudang" onchange="" required>
                                @if ($model->fromWarehouse)
                                    <option value="{{ $model->fromWarehouse->id }}">{{ $model->fromWarehouse->nama }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>

                    <div class="row my-5" id="item-content">

                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="to" id="to" label="Ke" required>
                                @if ($model->toWarehouse)
                                    <option value="{{ $model->toWarehouse->id }}">{{ $model->toWarehouse->nama }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label for="note" class="form-label">Catatan</label>
                                <textarea type="text" rows="5" class="form-control" name="note" id="note" placeholder="Masukkan Catatan" required>{{ $model->note }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>

                </form>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/admin/select/itemSelect.js?v=1.1') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    <script>
        $(document).ready(function() {

            let ITEM_INDEX = 0;
            let REAL_STOCK = [];
            let IDS_IN_USE = [];

            // initilize form elements
            const init = () => {
                checkClosingPeriod($('#date'));

                initSelect2Search('from', `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"
                });
                initSelect2Search('to', `{{ route('admin.select.ware-house') }}`, {
                    id: "id",
                    text: "nama"

                });

                get_existing_data();
            };

            // handle from warehouse change. will trigger all item form. so each item will remove all current stock and replace with new stock
            $('#from').change(function(e) {
                e.preventDefault();
                for (let index = 0; index < ITEM_INDEX; index++) {
                    $(`#item_id_${index}`).trigger('change');
                }
            });

            // handle event listener each form item
            const handleItemForm = (index) => {
                inititemSelect(`item_id_${index}`, 'all', 'purchase item', {
                    not_in_ids: IDS_IN_USE,
                })

                // * handle item select. will get the item unit and item stock information in a warehouse
                $(`#item_id_${index}`).change(function(e) {
                    e.preventDefault();
                    
                    conditionIfItemInUse(index);
                    if (this.value) {

                        $.ajax({
                            type: "get",
                            url: "{{ route('admin.stock-transfer.check-stock') }}",
                            data: {
                                item_id: this.value,
                                ware_house_id: $('#from').val(),
                            },
                                success: function(response) {
                                if (response?.data) {
                                    let {
                                        item_unit,
                                        stock_final
                                    } = response.data;

                                    $(`#unit-item-${index}`).html(item_unit.name);
                                    REAL_STOCK[index] = stock_final;
                                    $(`#stock_${index}`).val(stock_final);
                                    $(`#stock_${index}`).trigger('change');
                                    $(`#qty_${index}`).val(0);

                                    handleCheckTakenStockPending(index);
                                }
                            }
                        });
                        return;
                    }

                    $(`#stock_${index}`).val(0);
                    $(`#qty_${index}`).val(0);
                });

                // * handle stock change. will check the stock in warehouse and the stock will be transfered
                const handleCheckTakenStockPending = (index) => {
                    const id_from = $('#from').val();
                    const id_item = $(`#item_id_${index}`).val();
                    const url = "{{ route('admin.stock-transfer-check.item-stock-transfer', ['id_from' => ':id_from', 'id_item' => ':id_item']) }}".replace(':id_from', id_from).replace(':id_item', id_item);

                    $.ajax({
                        type: "POST",
                        url: url,
                        data: {
                            _token: "{{ csrf_token() }}",
                            id: "{{ $model->id }}",
                        },
                        success: function (response) {
                            const { success, taken_qty } = response;
                            
                            if (success) {
                                $(`#stock_${index}`).val(REAL_STOCK[index] - taken_qty);
                                $(`#stock_${index}`).trigger('change');
                                $(`#qty_${index}`).val(0);
                            }

                            $('#save-data').prop('disabled', false)
                        },
                        error: function (response) {
                            $('#save-data').prop('disabled', false)
                        }
                    })
                }

                // handle qty change will check the quantity in ware house and the stock will be transfered
                $(`#qty_${index}`).keyup(function(e) {

                    if (thousandToFloat(this.value) > thousandToFloat($(`#stock_${index}`).val())) {
                        $(this).val(0);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Jumlah transfer melebihi stok!',
                        });
                        return;
                    }
                });

                initCommasForm();
            };

            // * add item form
            const addItem = (index) => {
                ITEM_INDEX++;

                // setting the btn data (delete or add button)
                let btn = '';
                if (index == 0) {
                    btn = `
                        <div class="form-group">
                            <x-button type="button" color="primary" id='add-item' icon="plus" fontawesome size="sm" />
                        </div>`;
                } else {
                    btn = `
                        <div class="form-group">
                            <x-button type="button" color="danger" id="delete-item-${index}" icon="trash" fontawesome size="sm" />
                        </div>`;
                }

                let html = `
                    <div class="row wrapper-select" id='row-item-${index}' data-index="${index}">
                        <div class="col-md-3">
                            <x-select name="item_id[]" id="item_id_${index}" label="Item" required></x-select>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" label="stock" value="0" class="commas-form" name="stock[]" id="stock_${index}" readonly />
                                <div class="text-primary mt-1" id="unit-item-${index}"></div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" class="commas-form" name="qty[]" id="qty_${index}" label="Jumlah Transfer" required onkeyup="" />
                            </div>
                        </div>
                        <div class="col-md-2 flex align-self-end">
                            ${btn}
                        </div>
                    </div>
                `;

                $('#item-content').append(html);

                // handle button data (delete or add)
                if (index == 0) {
                    $('#add-item').click(function(e) {
                        e.preventDefault();
                        addItem(ITEM_INDEX)
                    });
                } else {
                    $(`#delete-item-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteItem(index);
                    });
                }

                handleItemForm(index);
            };

            const deleteItem = (index) => {
                console.log(index);
                $(`#row-item-${index}`).remove();
                IDS_IN_USE = [];
                conditionIfItemInUse(index);
            };

            const get_existing_data = () => {
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.stock-transfer.edit', $model->id) }}",
                    success: function(response) {

                        $.each(response.details, function(key, value) {
                            let index = ITEM_INDEX;

                            // setting the btn data (delete or add button)
                            let btn = '';
                            if (index == 0) {
                                btn = `
                                    <div class="form-group">
                                        <x-button type="button" color="primary" id='add-item' icon="plus" fontawesome size="sm" />
                                    </div>`;
                            } else {
                                btn = `
                                    <div class="form-group">
                                        <x-button type="button" color="danger" id="delete-item-${index}" icon="trash" fontawesome size="sm" />
                                    </div>`;
                            }

                            let html = `
                                    <div class="row" id='row-item-${index}'>
                                        <div class="col-md-3">
                                            <x-select name="item_id[]" id="item_id_${index}" label="Item" required>
                                                <option value="${value.item_id}">${value.item.nama}</option>
                                            </x-select>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" label="stock" value="0" class="commas-form" name="stock[]" id="stock_${index}" readonly value="${formatRupiahWithDecimal(value.stock??0)}" />
                                                <div class="text-primary mt-1" id="unit-item-${index}"></div>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <x-input type="text" class="commas-form" name="qty[]" id="qty_${index}" label="Jumlah Transfer" required onkeyup="" value="${formatRupiahWithDecimal(value.qty)}" />
                                            </div>
                                        </div>
                                        <div class="col-md-2 flex align-self-end">
                                            ${btn}
                                        </div>
                                    </div>
                                `;

                            $('#item-content').append(html);

                            handleItemForm(index);
                            ITEM_INDEX++;

                            // handle button data (delete or add)
                            if (index == 0) {
                                $('#add-item').click(function(e) {
                                    e.preventDefault();
                                    addItem(ITEM_INDEX)
                                });
                            } else {
                                $(`#delete-item-${index}`).click(function(e) {
                                    e.preventDefault();
                                    deleteItem(index);
                                });
                            }

                        });
                    }
                })
            }

            const conditionIfItemInUse = (iteration) => {
                $('[name="item_id[]"]').each(function (index, element) {
                    if (!IDS_IN_USE.includes(parseInt($(element).val()))) {
                        IDS_IN_USE[index] = parseInt($(element).val() ?? '0');
                    }

                    setTimeout(() => {
                        inititemSelect(`${$(element).attr('id')}`, 'all', 'purchase item', {
                            not_in_ids: IDS_IN_USE,
                        })
                    }, 50);
                });
            }
            init()
        });
    </script>

    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#stock-transfer');
    </script>
@endsection
