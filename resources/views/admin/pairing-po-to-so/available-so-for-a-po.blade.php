@extends('layouts.admin.layout.index')

@php
    $main = 'pairing-so-to-po';
@endphp

@section('title', Str::headline('Pairing purchase order to sale orders ') . '- ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.purchase-order.index') }}">{{ Str::headline('Po Trading') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.purchase-order.show', $po->po_trading) }}">{{ Str::headline('Po Trading Detail') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Pairing Po Trading Detail to So Trading Detail') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="Pairing purchase order to sale orders">
        <x-slot name="table_content">
            <div class="row">

                <div class="col-md-4">
                    <div class="row flex-column">
                        <div class="col-12 mb-30" id="list-po">
                            <h4>Available Sale Order</h4>
                            @include('components.validate-error')
                            <form action="">
                                <div class="row">

                                    <select name="" id="model_list" multiple class="form-control select2">
                                        @forelse ($model as $item)
                                            <option value="{{ $item->id }}">
                                                {{ $item->so_trading->customer->nama }} - {{ $item->so_trading->nomor_so }} / {{ formatNumber($item->jumlah - $item->sudah_dialokasikan) }}
                                            </option>
                                        @empty
                                            <option selected>No Data</option>
                                        @endforelse
                                    </select>

                                    @foreach ($model as $item)
                                        <input type="hidden" name="" id="po_trading_code" value="{{ $item->so_trading->nomor_so }}">
                                        <input type="hidden" name="" id="po_trading_jumlah" value="{{ $item->jumlah }}">
                                        <input type="hidden" name="" id="po_trading_tersedia" value="{{ (float) $item->jumlah - $item->sudah_dialokasikan }}">
                                        <input type="hidden" name="" id="po_trading_detail_id" value="{{ $item->id }}">
                                        <input type="hidden" name="" id="customer_form" value="{{ $item->so_trading->customer->nama }}">
                                    @endforeach

                                </div>
                            </form>

                            <x-button color="primary" icon='link' fontawesome id="pairing-btn" label="pairing" class="float-end" />
                        </div>
                        <div class="col-12">
                            <h4>Purchase Order Item Detail</h4>
                            <x-table>
                                <x-slot name="table_body">
                                    <tr>
                                        <th>{{ Str::headline('nomor Po') }}</th>
                                        <td>{{ $po->po_trading->nomor_po }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('customer') }}</th>
                                        <td>{{ $po->po_trading->customer->nama }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('sh_number') }}</th>
                                        <td>{{ $po->po_trading->sh_number->kode }}</td>
                                    </tr>
                                    @foreach ($po->po_trading->sh_number->sh_number_details as $item)
                                        <tr>
                                            <th>{{ Str::headline($item->type) }}</th>
                                            <td>{{ $item->alamat }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th>{{ Str::headline('Item') }}</th>
                                        <td>{{ Str::headline($po->item->nama) }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('Jumlah') }}</th>
                                        <td>{{ formatNumber($po->jumlah, 2, '.', '.') }} / {{ $po->type }}</td>
                                        <input type="hidden" name="jumlah" value="{{ ($po->type == 'Kilo Liter' ? $po->jumlah * 1000 : $po->jumlah) - $po->sudah_dialokasikan }}" id="jumlah">
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('stock') }}</th>
                                        <td>{{ formatNumber($po->alokasi_tersedia, 2, '.', '') }} {{ $po->item->unit->name ?? '' }}</td>
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
                                    <th>{{ Str::headline('Kode purchase order') }}</th>
                                    <th>{{ Str::headline('Di Alokasi') }}</th>
                                    <th>{{ Str::headline('sisa') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <td>{{ $po->po_trading->nomor_po }}</td>
                                        <td>
                                            <span id="resume-alokasi">0</span>
                                            <span> {{ $po->po_trading->po_trading_detail->item->unit->name ?? '' }}</span>
                                        </td>
                                        <td id="">
                                            <span>
                                                <span id="resume-sisa">{{ formatNumber($po->alokasi_tersedia, 2, '.', '') }}</span>
                                                <span> {{ $po->po_trading->po_trading_detail->item->unit->name ?? '' }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <x-button color="primary" icon='fa-circle-arrow-right' label="save data" />
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
                                            <th class="col-md-4">Jumlah</th>
                                            <th class="col-md-4">Kuantitas Tersedia</th>
                                        </tr>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        <tr>
                                            <td>${data.customer_form}</td>
                                            <td>${numberWithCommas(data.po_trading_jumlah)} </td>
                                            <td>${numberWithCommas(data.po_trading_tersedia)}</td>
                                        </tr>
                                    </x-slot>
                                </x-table>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="alokasi-${key}" name="alokasi[]" class="commas-form" label="alokasi" required/>
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
                            $('#resume-alokasi').html('');
                            $('#resume-sisa').html('');

                            // if form on submit return unsubmit
                            $('#form-pairing').submit(function(e) {
                                e.preventDefault();
                                return;
                            });

                            current_total -= value;
                            total_list_value[key] = 0;
                            $(this).val(0);
                            alert('alokasi melebihi jumlah kuantitas po tersedia');
                            $('#form-pairing').unbind('submit');
                            return;
                        }

                        // show alert if po_trading_tersedia is less than current total
                        if (value > data.po_trading_tersedia) {
                            $('#resume-alokasi').html('');
                            $('#resume-sisa').html('');

                            // if form on submit return unsubmit
                            $('#form-pairing').submit(function(e) {
                                e.preventDefault();
                                return;
                            });

                            current_total -= value;
                            total_list_value[key] = 0;
                            $(this).val(0);
                            alert('alokasi melebihi jumlah so');
                            $('#form-pairing').unbind('submit');
                            return;
                        }

                        // update total list value
                        total_list_value[key] = value;
                    });
                })
                initCommasForm()
            }

            const po_trading_codes = [...document.querySelectorAll('#po_trading_code')];
            const po_trading_tersedia = [...document.querySelectorAll('#po_trading_tersedia')];
            const po_trading_jumlah = [...document.querySelectorAll('#po_trading_jumlah')];
            const customer_form = [...document.querySelectorAll('#customer_form')];

            $('#pairing-btn').click((e) => {
                [...document.getElementById('model_list').options].map((options, key) => {
                    if (options.selected) {
                        main_data.push({
                            po_trading_id: parseInt(options.value),
                            po_trading_code: po_trading_codes[key].value,
                            po_trading_tersedia: parseFloat(po_trading_tersedia[key].value).toFixed(2),
                            po_trading_jumlah: parseFloat(po_trading_jumlah[key].value).toFixed(2),
                            customer_form: customer_form[key].value
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
                    alert('Please select some so trading to pair');
                }
            });
        });
    </script>

    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#po-pairing')
    </script>
@endsection
