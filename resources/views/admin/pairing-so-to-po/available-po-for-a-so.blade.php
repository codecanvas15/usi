@extends('layouts.admin.layout.index')

@php
    $main = 'pairing-so-to-po';
@endphp

@section('title', Str::headline('Pairing sales order to purchase order ') . '- ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.sales-order.index') }}">{{ Str::headline('So Trading') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.sales-order.show', $so->so_trading) }}">{{ Str::headline('So Trading Detail') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Pairing So Trading Detail to Po Trading Detail') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="Pairing sales order to purchase order">
        <x-slot name="table_content">
            <div class="row">

                <div class="col-md-4">
                    <div class="row flex-column">

                        <div class="col-12 mb-30" id="list-po">
                            <h4>Available Purchase Order</h4>
                            @include('components.validate-error')
                            <form action="">
                                <div class="row">

                                    <select name="" id="model_list" multiple class="form-control select2">
                                        @forelse ($model as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->po_trading->customer->nama }} - {{ $item->po_trading->nomor_po }}/ {{ formatNumber($item->alokasi_tersedia) }} {{ $item->type }}
                                            </option>
                                        @empty
                                            <option selected>No Data</option>
                                        @endforelse
                                    </select>

                                    @foreach ($model as $item)
                                        <input type="hidden" name="" id="po_trading_code" value="{{ $item->po_trading->nomor_po }}">
                                        <input type="hidden" name="" id="po_trading_jumlah" value="{{ $item->jumlah }}">
                                        <input type="hidden" name="" id="po_trading_tersedia" value="{{ $item->alokasi_tersedia }}">
                                        <input type="hidden" name="" id="po_trading_type" value="{{ $item->type }}">
                                        <input type="hidden" name="" id="po_trading_detail_id" value="{{ $item->id }}">
                                        <input type="hidden" name="" id="nama_customer" value="{{ $item->po_trading->customer->nama }}">
                                        <input type="hidden" name="" id="sh_number" value="{{ $item->po_trading->sh_number->kode }}">
                                    @endforeach
                                </div>
                            </form>

                            <x-button color="primary" icon='link' fontawesome id="pairing-btn" label="pairing" class="float-end" />
                        </div>

                        <div class="col-12">
                            <h4>Sale Order Item Detail</h4>
                            <x-table>
                                <x-slot name="table_body">
                                    <tr>
                                        <th>{{ Str::headline('nomor so') }}</th>
                                        <td>{{ $so->so_trading->nomor_so }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('customer') }}</th>
                                        <td>{{ $so->so_trading->customer->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('sh_number') }}</th>
                                        <td>{{ $so->so_trading->sh_number->kode }}</td>
                                    </tr>
                                    @foreach ($so->so_trading->sh_number->sh_number_details as $item)
                                        <tr>
                                            <th>{{ Str::headline($item->type) }}</th>
                                            <td>{{ $item->alamat }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th>{{ Str::headline('Item') }}</th>
                                        <td>{{ Str::headline($so->item->nama) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('Jumlah') }}</th>
                                        <td>{{ formatNumber($so->jumlah, 2, '.', '.') }} {{ $so->item->unit->name ?? '' }} </td>
                                        <input type="hidden" name="jumlah" value="{{ $so->jumlah - $so->sudah_dialokasikan }}" id="jumlah">
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('sisa pairing') }}</th>
                                        <td>{{ formatNumber($so->alokasi_tersedia, 2, '.', '.') }} {{ $so->item->unit->name ?? '' }}</td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>
                    </div>
                </div>

                <div class="col-md-8" id="card-pairing">
                    <h4>Alokasi Order</h4>
                    <form action="" method="POST" id="form-pairing">
                        @csrf
                        <div id="pairing">

                            <div id="text-pairing-none" class="text-center text-danger">
                                <h5>Please select some po trading to pair</h5>
                            </div>
                        </div>

                        <div class="mt-10" id="resume-pairing">
                            <x-table theadColor='danger' id="resume-pairing-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('Kode Sale Order') }}</th>
                                    <th>{{ Str::headline('Di Alokasi') }}</th>
                                    <th>{{ Str::headline('sisa') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <td>{{ $so->so_trading->nomor_so }}</td>
                                        <td>
                                            <span id="resume-alokasi"></span><span>{{ $so->item->unit->name ?? '' }}</span>
                                        </td>
                                        <td id="">
                                            <span>
                                                <span id="resume-sisa">
                                                    {{ formatNumber($so->alokasi_tersedia, 2, '.', '.') }}
                                                </span>
                                                <span>
                                                    {{ $so->item->unit->name ?? '' }}
                                                </span>
                                            </span>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <x-button type="submit" color="primary" icon='fa-circle-arrow-right' label="save data" />
                        </div>
                    </form>
                </div>

            </div>

        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#card-pairing').hide();

            let jumlah = parseFloat($('#jumlah').val()).toFixed(2);
            let current_total = 0;
            let total_list_value = [];

            let main_data = [
                // {
                //     po_trading_id : 1,
                //     po_trading_code : "PO-001",
                //     po_trading_tersediat : 1,
                //     type : ""
                // },
            ];

            const displayFormPairing = () => {
                $('#card-pairing').show();
                main_data.map((data, key) => {
                    $('#pairing').append(`
                        <div class="row">
                            <div class="col-12 ${key != 0 ? 'mt-20' : ''}">
                                <h5>${data.po_trading_code}</h5>
                                <x-table>
                                    <x-slot name="table_head">
                                        <tr>
                                            <th class="col-md-4">Customer</th>
                                            <th class="col-md-4">Stock</th>
                                        </tr>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <td>${data.sh_number} - ${data.nama_customer}</td>
                                            <td>${numberWithCommas(data.po_trading_tersedia)}</td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="alokasi-${key}" class="commas-form" name="alokasi[]" label="alokasi" required/>
                                    <input type="hidden" id="type-${key}" name="type[]" value="${data.type}">
                                    <input type="hidden" name="po_trading_detail_id[]" value="${data.po_trading_id}">
                                </div>
                            </div>
                        </div>
                    `);

                    $(`#alokasi-${key}`).keyup(function(e) {
                        e.preventDefault();

                        let value = thousandToFloat($(this).val());
                        if (isNaN(value)) {
                            $(this).val(0);
                            return;
                        }

                        // if total list value not undefined reset the current value from total list value[index]
                        if (total_list_value[key] != undefined) {
                            current_total -= total_list_value[key];
                        } else {
                            total_list_value[key] = 0;
                        }

                        // increase current total
                        current_total += value;

                        $('#resume-alokasi').html(numberWithCommas(current_total));
                        $('#resume-sisa').html(numberWithCommas(jumlah - current_total));

                        // show alert if current total is greater than jumlah
                        if (current_total > jumlah) {
                            $('#resume-alokasi').html(0);
                            $('#resume-sisa').html(0);
                            // if form on submit return unsubmit
                            $('#form-pairing').submit(function(e) {
                                e.preventDefault();
                                return;
                            });

                            current_total -= value;
                            total_list_value[key] = 0;
                            $(this).val(0);
                            alert('Jumlah alokasi melebihi jumlah so');

                            // reset even submit
                            $('#form-pairing').unbind('submit');
                            return;
                        }

                        // show alert if po_trading_tersedia is less than current total
                        if (value > data.po_trading_tersedia) {
                            $('#resume-alokasi').html(0);
                            $('#resume-sisa').html(0);

                            // if form on submit return unsubmit
                            $('#form-pairing').submit(function(e) {
                                e.preventDefault();
                                return;
                            });

                            current_total -= value;
                            total_list_value[key] = 0;
                            $(this).val(0);
                            alert('PO Trading tidak cukup untuk mengisi alokasi');

                            // reset even submit
                            $('#form-pairing').unbind('submit');
                            return;
                        }

                        // update total list value
                        total_list_value[key] = value;
                    });
                });


                initCommasForm()
            };

            const po_trading_codes = [...document.querySelectorAll('#po_trading_code')];
            const po_trading_tersedia = [...document.querySelectorAll('#po_trading_tersedia')];
            const po_trading_jumlah = [...document.querySelectorAll('#po_trading_jumlah')];
            const po_trading_type = [...document.querySelectorAll('#po_trading_type')];
            const nama_customer = [...document.querySelectorAll('#nama_customer')];
            const sh_number = [...document.querySelectorAll('#sh_number')];

            $('#pairing-btn').click((e) => {
                main_data = [];
                [...document.getElementById('model_list').options].map((options, key) => {
                    if (options.selected) {
                        main_data.push({
                            po_trading_id: parseInt(options.value),
                            po_trading_code: po_trading_codes[key].value,
                            po_trading_tersedia: parseFloat(po_trading_tersedia[key].value).toFixed(2),
                            po_trading_jumlah: parseFloat(po_trading_jumlah[key].value).toFixed(2),
                            po_trading_type: po_trading_type[key].value,
                            nama_customer: nama_customer[key].value,
                            sh_number: sh_number[key].value,
                        });
                    }
                });

                if (main_data.length > 0) {
                    $('#pairing-btn').remove();
                    $('#text-pairing-none').remove();
                    $('#list-po').remove();
                    $('#text-helper').remove();
                    displayFormPairing();
                    return;
                } else {
                    alert('Please select some po trading to pair');
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#so-pairing')
    </script>
@endsection
