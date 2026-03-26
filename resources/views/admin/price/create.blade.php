@extends('layouts.admin.layout.index')

@php
    $main = 'price';
    $title = 'harga';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.store") }}" method="post" id="create">

        <x-card-data-table title="{{ 'tambah ' . $title }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <x-select label="tahun" id="tahun" name="tahun" required>
                            <option value="">Pilih data</option>
                            @foreach (range(Date('Y'), Date('Y', strtotime('+10 years'))) as $item)
                                <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-select label="period" id="period_id" name="period_id" required>

                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="harga_beli" name="harga_beli" class="commas-form" value="{{ $model->harga_beli ?? '' }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="harga_jual" name="harga_jual" class="commas-form" value="{{ $model->harga_jual ?? '' }}" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <x-select label="item" id="item_id" name="item_id" required>
                        </x-select>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
        <div id="price-customer">
            <x-card-data-table title="Price Customer">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="nama" name="nama" value="{{ $model->nama ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-3 row align-items-end">
                            <div class="form-group">
                                <x-button link="#" color="primary" id="add-price-customer" icon="plus" size="sm" fontawesome />
                            </div>
                        </div>
                    </div>

                    <div id="price-customer-form">

                    </div>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end">
                        <x-button color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>
    </form>

@endsection

@section('js')
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.money').mask('000.000.000.000.000,00', {
                reverse: true
            });

            let form_first_loaded = false;

            // price card ==================================================================================
            $('#btn-save-price').hide();
            let form_val = {};

            const checkPriceCard = () => {
                if (form_val.tahun && form_val.period_id && form_val.harga_beli && form_val.harga_jual && form_val.item_id) {
                    $('#btn-save-price').show();

                    $('#price-customer').fadeIn(500);

                    if (!form_first_loaded) {
                        $('#add-price-customer').click();
                        form_first_loaded = true;
                        // addPriceCustomerForm();
                    }
                } else {
                    $('#btn-save-price').hide();
                }
                return;
            }

            $('#tahun').change(function(e) {
                form_val['tahun'] = $(this).val();
                checkPriceCard()
            });
            $('#period_id').change(function(e) {
                form_val['period_id'] = $(this).val();
                checkPriceCard()
            });
            $('#harga_beli').on('change click blur keypress', function(e) {
                form_val['harga_beli'] = $(this).val();
                checkPriceCard()
            });
            $('#harga_jual').on('change click blur keypress', function(e) {
                form_val['harga_jual'] = $(this).val();
                checkPriceCard()
            });
            $('#item_id').change(function(e) {
                form_val['item_id'] = $(this).val();
                checkPriceCard()
            });

            $('#price-customer').hide(500);

            $('#tahun').change(function(e) {
                if ($(this).val().length == 4) {
                    initSelect2Search(`period_id`, "{{ route('admin.select.period') }}/" + $(this).val(), {
                        id: "id",
                        text: "value"
                    });
                }
            });

            inititemSelect('item_id', 'trading')

            $('#add-price-customer').click(function(e) {
                e.preventDefault();
                addPriceCustomerForm();
            });

            // price card ==================================================================================

            // price customer  =============================================================================
            let form_val_price_customer_count = 0;

            const addPriceCustomerForm = () => {

                let html = `<div class="row mt-30" id="price-customer-detail-${form_val_price_customer_count}">
                                <div class="col-md-3">
                                    <x-select label="sh no." id="sh_number_${form_val_price_customer_count}" name="sh_number_id[]" dataSpecial='${form_val_price_customer_count}' required></x-select>
                                </div>
                                <div class="col-md-3">
                                    <x-input type="text" label="customer" id="nama_customer_${form_val_price_customer_count}" value="" name="customer" required disabled />
                                </div>
                                <div class="col-md-3">
                                    <x-input type="text" label="drop_point / ship to" id="drop_point_${form_val_price_customer_count}" value="" name="drop_point" required disabled />
                                </div>
                                <div class="col-md-3">
                                    <div class="row">
                                        <div class="col-8">
                                            <x-input type="text" label="supply_point" id="supply_point_${form_val_price_customer_count}" value="" name="supply_point" required disabled />
                                        </div>
                                        <div class="col row align-items-end">
                                            <x-button link="#" color="danger" id="delete-price-${form_val_price_customer_count}" icon="trash" class="w-auto" size="sm" fontawesome />
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                $('#price-customer-form').append(html);

                let index = form_val_price_customer_count;
                $(`#delete-price-${form_val_price_customer_count}`).on('click', function(e) {
                    $(`#price-customer-detail-${index}`).remove();
                });

                const initSelect2SearchSHNumber = (target, route, selector) => {
                    let selected_item = [];

                    $(`select[name="#${target}"]`)
                        .toArray()
                        .map(function() {
                            if ($(this).val() != null) {
                                selected_item.push($(this).val());
                            }
                        });

                    let target_value = $(`#${target}`).val();

                    var itemSelect = {
                        placeholder: "Pilih Data",
                        minimumInputLength: 0,
                        allowClear: true,
                        language: {
                            inputTooShort: () => {
                                return "Insert at least 3 characters";
                            },
                            noResults: () => {
                                return "Data can't be found";
                            },
                        },
                        ajax: {
                            url: route,
                            dataType: "json",
                            delay: 250,
                            type: "get",
                            data: ({
                                term
                            }) => {
                                let result = {};
                                result["search"] = term;
                                result["selected_item"] = selected_item;
                                result[target] = target_value;
                                return result;
                            },
                            processResults: ({
                                data
                            }) => {
                                let final_data = data.map((data, key) => {
                                    return {
                                        id: data[selector.id],
                                        text: data.kode + ' - ' + data.customer.nama,
                                    };
                                });
                                return {
                                    results: final_data,
                                };
                            },
                            cache: true,
                        },
                    };

                    $(`#${target}`).select2(itemSelect);
                    return;
                };

                initSelect2SearchSHNumber(`sh_number_${form_val_price_customer_count}`, "{{ route('admin.select.sh-number') }}", {
                    id: "id",
                });

                $(`#sh_number_${form_val_price_customer_count}`).change(function(e) {
                    e.preventDefault();
                    let dataSpecial = $(this).data('special');
                    $.ajax({
                        type: "get",
                        url: "{{ route('admin.sh-number.detail') }}/" + $(this).val(),
                        success: function({
                            data
                        }) {
                            $(`#nama_customer_${dataSpecial}`).val(data.customer.nama);
                            data.sh_number_details.map((detail, key) => {
                                if (detail.type == 'Drop Point') {
                                    $(`#drop_point_${dataSpecial}`).val(detail.alamat);
                                }
                                if (detail.type == 'Supply Point') {
                                    $(`#supply_point_${dataSpecial}`).val(detail.alamat);
                                }
                            });

                            return;
                        }
                    });

                    return;
                });

                form_val_price_customer_count++;
                return;
            }
            // price customer  =============================================================================
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#price')
    </script>
@endsection
