@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-trading';
@endphp

@section('title', Str::headline("Create $main") . ' - ')

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
                        {{ Str::headline('Create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.new-store") }}" method="post">
        <input type="hidden" name="delivery_order_id" value="{{ $delivery_order_id }}">
        @csrf
        <x-card-data-table title="Create {{ $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <x-table theadColor="danger">
                    <x-slot name="table_head">
                        <th class="col-md-4"></th>
                        <th class="col-md"></th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('customer') }}</th>
                            <td>{{ $so_trading->customer->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('no. sales order') }}</th>
                            <td><a target="_blank" href="{{ route('admin.sales-order.show', ['sales_order' => $so_trading->id]) }}">{{ $so_trading->nomor_so }}</a>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('no. po external') }}</th>
                            <td>{{ $so_trading->nomor_po_external }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('item') }}</th>
                            <td>{{ $so_trading->so_trading_detail->item->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('lost tolerance') }}</th>
                            <td>{{ $lost_tolerance_type == 'percent' ? $lost_tolerance * 100 : $lost_tolerance }}
                                {{ $lost_tolerance_type }}</td>
                        </tr>
                    </x-slot>
                </x-table>

                <x-table theadColor='dark'>
                    <x-slot name="table_head">
                        <th class="col-md-4" colspan="6">DELIVERY ORDER</th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('tanggal muat') }}</th>
                            <th>{{ Str::headline('tanggal bongkar') }}</th>
                            <th>{{ Str::headline('no. DO') }}</th>
                            <th class="text-end">{{ Str::headline('jumlah dikirm') }}</th>
                            <th class="text-end">{{ Str::headline('jumlah diterima') }}</th>
                            <th class="text-end">{{ Str::headline('lost') }}</th>
                        </tr>
                        @foreach ($delivery_orders as $delivery_order)
                            <tr>
                                <td>{{ localDate($delivery_order->tanggal_muat) }}</td>
                                <td>{{ localDate($delivery_order->tanggal_bongkar) }}</td>
                                <td>{{ $delivery_order->nomor_do }}</td>
                                <td class="text-end">{{ floatDotFormat($delivery_order->kuantitas_kirim) }}</td>
                                <td class="text-end">{{ floatDotFormat($delivery_order->kuantitas_diterima) }}</td>
                                <td class="text-end">
                                    {{ $delivery_order->kuantitas_kirim - $delivery_order->kuantitas_diterima }}</td>
                            </tr>
                        @endforeach
                    </x-slot>
                    <x-slot name="table_foot">
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-end">
                                {{ Str::headline('total jumlah dikirm') }}
                            </th>
                            <th class="text-end">
                                {{ floatDotFormat($total_jumlah_dikirim) }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-end">
                                {{ Str::headline('total jumlah diterima') }}
                            </th>
                            <th class="text-end">
                                {{ floatDotFormat($total_jumlah_diterima) }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-end">
                                {{ Str::headline('total lost') }}
                            </th>
                            <th class="text-end">
                                {{ floatDotFormat($total_lost) }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-end">
                                {{ Str::headline('lost tolerance') }}
                            </th>
                            <th class="text-end">
                                {{ floatDotFormat($tolerance_amount) }}
                            </th>
                        </tr>
                        <tr>
                            <th colspan="4"></th>
                            <th class="text-end">
                                {{ Str::headline('total akhir') }}
                            </th>
                            <th class="text-end">
                                {{ floatDotFormat($jumlah) }}
                            </th>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>
            <x-slot name="footer">

            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="INVOICE. {{ $kode }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="date" name="date" label="Inv. Date" required value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" />
                        </div>
                        <div class="form-group">
                            <x-input class="datepicker-input" id="due_date" name="due_date" label="Inv. Due Date" required value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" />
                        </div>

                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="bank_internal_id" id="bank_internal_id" label="Bank Payment" required autofocus>
                                @foreach ($bank_internals as $bank_internal)
                                    <option value="{{ $bank_internal->id }}">{{ $bank_internal->nama_bank }}
                                        {{ $bank_internal->no_rekening }} - {{ $bank_internal->on_behalf_of }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                </div>
                <table class="table table-striped mt-10 mb-50">
                    <thead class="bg-dark">
                        <tr>
                            <th>{{ Str::headline('item') }}</th>
                            <th>{{ Str::headline('jumlah') }}</th>
                            <th class="text-end">{{ Str::headline('harga') }}</th>
                            <th class="text-end">{{ Str::headline('total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{ $item->nama }}</td>
                            <td class="text-end">{{ floatDotFormat($jumlah) }}</td>
                            <td class="text-end">{{ floatDotFormat($harga) }}</td>
                            <td class="text-end">{{ floatDotFormat($subtotal) }}</td>
                        </tr>
                        @foreach ($so_trading_taxes as $so_trading_tax)
                            <tr>
                                <td>{{ $so_trading_tax->tax->name }}</td>
                                <td class="text-end">{{ floatDotFormat($so_trading_tax->value * 100) }}%</td>
                                <td class="text-end"></td>
                                <td class="text-end">{{ floatDotFormat($so_trading_tax->amount) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="bg-dark">
                        <tr>
                            <th colspan="3" class="text-end">Sub Total</th>
                            <th class="text-end">
                                <div>
                                    <span>{{ floatDotFormat($after_additional_tax) }}</span>
                                    <input type="hidden" id="subTotal" class="sub-total" name="sub_total" value="{{ replaceDot(formatNumber($after_additional_tax)) }}" />
                                </div>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <table class="table table-striped">
                    <thead class="bg-dark">
                        <tr>
                            <th>#</th>
                            <th>{{ Str::headline('additional item') }}</th>
                            <th>{{ Str::headline('jumlah') }}</th>
                            <th class="text-end">{{ Str::headline('harga') }}</th>
                            <th class="text-end">{{ Str::headline('sub total') }}</th>
                            <th>{{ Str::headline('tax') }}</th>
                            <th>{{ Str::headline('value') }}</th>
                            <th class="text-end">{{ Str::headline('total') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($so_trading->sale_order_additionals as $key => $item)
                            <tr>
                                <td>
                                    {{ $key++ + 1 }}
                                    <input type="hidden" name="addon_item_ids[]" value="{{ $item->id }}">
                                </td>
                                <td>{{ $item->item?->nama }}</td>
                                <td>
                                    <div>
                                        <input type="text" id="addOnItemQty{{ $key }}" class="add-on-item-qty" name="addon_item_qty[]" data-testId="{{ $key }}" placeholder="Quantity" value="{{ replaceDot(formatNumber($item->quantity)) }}" />
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div>
                                        <span>{{ floatDotFormat($item->price) }}</span>
                                        <input type="hidden" id="addOnItemPrice{{ $key }}" class="add-on-item-price{{ $key }}" name="addon_item_price[]" value="{{ replaceDot(formatNumber($item->price)) }}" />
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div>
                                        <span id="addOnItemSpanSubTotal{{ $key }}">{{ floatDotFormat($item->sub_total) }}</span>
                                        <input type="hidden" id="addOnItemSubTotal{{ $key }}" class="add-on-item-sub-total" name="addon_item_sub_total[]" value="">
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
                                            <p class="mb-5">
                                                <input type="hidden" name="addon_item_{{ $item->id }}_tax_ids[]" value="{{ $item_tax->tax_id }}">
                                                {{ $item_tax->tax?->name }}
                                                {{ $item_tax->value * 100 }}%
                                                <input type="hidden" name="addon_item_{{ $item->id }}_tax_value[]" value="{{ $item_tax->value }}">
                                                <input type="hidden" class="add-on-item-tax{{ $key }}" name="addon_item_{{ $item->id }}_tax_total[]" value="{{ replaceDot(formatNumber($item_tax->total)) }}">
                                            </p>
                                        @empty
                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                        @endforelse
                                    </div>
                                </td>
                                <td>
                                    <div>
                                        @forelse ($item->sale_order_additional_taxes as $item_tax)
                                            <p class="mb-5"class="mb-4">
                                                {{ floatDotFormat($item_tax->total) }}
                                            </p>
                                        @empty
                                            <x-button size="sm" badge color="danger" label="no Tax" />
                                        @endforelse
                                    </div>
                                </td>
                                <td class="text-end">
                                    <div>
                                        <span id="addOnItemSpanTotal">{{ floatDotFormat($item->total) }}</span>
                                        <input type="hidden" id="addOnItemTotal" class="add-on-item-total{{ $key }}" name="addon_item_total[]" value="{{ replaceDot(formatNumber($item->total)) }}">
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <td class="text-end" colspan="7">Dpp</td>
                            <td id="addOnDPP" class="text-end">{{ floatDotFormat($so_trading->sale_order_additionals->sum('sub_total')) }}</td>
                        </tr>
                        <tr>
                            <td class="text-end" colspan="7">Total pajak</td>
                            <td class="text-end">
                                {{ floatDotFormat($so_trading->additional_tax_total) }}
                                <input type="hidden" id="addOnTaxTotal" class="add-on-tax-total" name="addon_tax_total" value="{{ replaceDot(formatNumber($so_trading->additional_tax_total)) }}">
                            </td>
                        </tr>
                        <tr class="bg-dark">
                            <th colspan="7" class="text-end">Sub Total</th>
                            <th class="text-end">
                                <div>
                                    <span id="addOnSpanSubTotal">{{ floatDotFormat($so_trading->other_cost) }}</span>
                                    <input type="hidden" id="addOnSubTotal" class="add-on-sub-total" name="addon_sub_total" value="{{ replaceDot(formatNumber($so_trading->other_cost)) }}">
                                </div>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div class="row justify-content-end mt-30">
                    <div class="col-md-6 col-lg-3">
                        <x-table theadColor='dark'>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th class="text-end">Grand Total</th>
                                    <td class="text-end">
                                        <span id="grandTotalSpan">{{ floatDotFormat($after_additional_tax + $so_trading->other_cost) }}</span>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                    <x-button type="submit" color="primary" label="Save data" />
                </div>
            </x-slot>

        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $('.add-on-item-qty').on('keyup', function() {
            let id = $(this).attr('data-testId');
            let qty = parseFloat($(`#addOnItemQty${id}`).val());
            let price = parseFloat($(`#addOnItemPrice${id}`).val());
            let subTotal = qty * price;
            $(`#addOnItemSpanSubTotal${id}`).text(numberWithCommas(subTotal.toFixed(2)));
            $(`#addOnItemSubTotal${id}`).val(subTotal);
            calculateDPP();
            calculateTotal(id);
            calculateGrandTotal();
        });
        $('.add-on-item-qty').trigger('keyup');

        function calculateDPP() {
            let dpp = 0;
            $('.add-on-item-sub-total').each(function() {
                dpp += parseFloat($(this).val());
            });
            $('#addOnDPP').text(numberWithCommas(dpp.toFixed(2)));
            // calculate sub total additional item
            // dpp + total pajak
            $('#addOnSpanSubTotal').text(numberWithCommas((dpp + parseFloat($('#addOnTaxTotal').val())).toFixed(2)));
            $('#addOnSubTotal').val(dpp + parseFloat($('#addOnTaxTotal').val()));
        }
        // calculate total tax current item + sub total current item
        function calculateTotal(id) {
            let total = 0;
            $(`.add-on-item-tax${id}`).each(function() {
                total += parseFloat($(this).val());
            });
            total += parseFloat($(`#addOnItemSubTotal${id}`).val());
            $(`.add-on-item-total${id}`).val(total);
            $('#addOnItemSpanTotal').text(numberWithCommas(total.toFixed(2)));
        }

        function calculateGrandTotal() {
            let grandTotal = 0;
            grandTotal += parseFloat($('#subTotal').val());
            grandTotal += parseFloat($('#addOnSubTotal').val());
            $('#grandTotalSpan').text(numberWithCommas(grandTotal.toFixed(2)));
        }
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-trading')
    </script>
@endsection
