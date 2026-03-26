@extends('layouts.admin.layout.index')

@php
    $main = 'invoice-return';
    $title = 'retur penjualan';
@endphp

@section('title', Str::headline("edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        Retur/Adjustment
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.update", ['invoice_return' => $model->id]) }}" method="post" id="form-data">
        @csrf
        @method('PUT')
        <x-card-data-table title="{{ 'edit ' . $title }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route('admin.' . $main . '.create') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" id="branch_id" label="branch" required>
                                    <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ localDate($model->date) }}" onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="customer_id" id="customer_id" label="customer" required onchange="resetForm()">
                                    <option value="{{ $model->customer->id }}">{{ $model->customer->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="reference_parent_id" id="reference_parent_id" label="DO" required onchange="getDODetail($(this))">
                                    <option value="{{ $model->reference_data->id }}">{{ $model->reference_data->code }}</option>
                                </x-select>
                                <input type="hidden" name="type" id="type" value="{{ $model->type }}">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tanggal DO" name="delivery_date" id="delivery_date" class="datepicker-input" value="{{ localDate($model->reference_data->date_send ?? $model->reference_data->load_date) }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                    <option value="{{ $model->currency->id }}">{{ $model->currency->kode }} {{ $model->currency->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="kurs" name="exchange_rate" id="exchange_rate" readonly value="{{ formatNumber($model->exchange_rate) }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="nomor faktur pajak" name="tax_number" id="tax_number" onblur="check_unique_tax_number({{ $model->id }})" value="{{ $model->tax_number }}" />
                                <small class="text-danger">* jika terdapat pajak nomor faktur wajib diisi!</small>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="project_id" id="project_id" label="project">
                                    @if ($model->project)
                                        <option value="{{ $model->project->id }}">{{ $model->project->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="note" name="referensi" id="referensi" value="{!! $model->reference !!}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" id="ware_house_id" label="gudang" required>
                                    <option value="{{ $model->ware_house->id }}">{{ $model->ware_house->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-12 mt-2">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead class="bg-info">
                                        <tr>
                                            <th>{{ Str::headline('item') }}</th>
                                            <th>{{ Str::headline('unit') }}</th>
                                            <th>{{ Str::headline('qty DO') }}</th>
                                            <th class="text-end">{{ Str::headline('qty') }}</th>
                                            <th class="text-end"></span> {{ Str::headline('price') }} <span class="currency_kode">{{ $model->currency->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('subtotal') }} <span class="currency_kode">{{ $model->currency->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('pajak') }} <span class="currency_kode">{{ $model->currency->kode }}</th>
                                            <th class="text-end"></span> {{ Str::headline('total') }} <span class="currency_kode">{{ $model->currency->kode }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="return-data">
                                        @foreach ($model->invoice_return_details as $key => $invoice_return_detail)
                                            <tr id="row-{{ $key }}">
                                                <td class="align-top">
                                                    <input type="hidden" name="invoice_return_detail_id[]" value="{{ $invoice_return_detail->id }}">
                                                    <input type="hidden" name="reference_model[]" value="{{ $invoice_return_detail->reference_model }}">
                                                    <input type="hidden" name="reference_id[]" value="{{ $invoice_return_detail->reference_id }}">
                                                    <input type="hidden" name="item_id[]" value="{{ $invoice_return_detail->item->id }}">
                                                    <input type="hidden" name="unit_id[]" value="{{ $invoice_return_detail->unit->id }}">
                                                    {{ $invoice_return_detail->item->nama }} <br>
                                                    {{ $invoice_return_detail->item->kode }}
                                                </td>

                                                <td class="align-top">
                                                    {{ $invoice_return_detail->unit->name }}
                                                </td>
                                                <td class="text-end align-top">
                                                    <input type="text" id="do_qty_{{ $key }}" name="do_qty[]" value="{{ formatNumber($invoice_return_detail->do_qty) }}" class="form-control text-end" readonly>
                                                    <br>
                                                    <span><b>QTY Retur/Terpakai :</b> {{ formatNumber($invoice_return_detail->return_qty) }}</span>
                                                    <input type="hidden" id="return_qty_{{ $key }}" name="return_qty[]" value="{{ formatNumber($invoice_return_detail->return_qty) }}">
                                                    <input type="hidden" id="rest_qty_{{ $key }}" name="rest_qty[]" value="{{ formatNumber($invoice_return_detail->do_qty - $invoice_return_detail->return_qty) }}">
                                                </td>
                                                <td class="text-end align-top">
                                                    <input type="text" id="qty_{{ $key }}" name="qty[]" class="form-control text-end commas-form" onkeyup="countRowTotal({{ $key }})" value="{{ formatNumber($invoice_return_detail->qty) }}">
                                                </td>
                                                <td class="text-end align-top">
                                                    <input type="text" id="price_{{ $key }}" name="price[]" value="{{ formatNumber($invoice_return_detail->price) }}" class="form-control text-end" readonly>
                                                    <input type="hidden" id="hpp_{{ $key }}" name="hpp[]" value="{{ formatNumber($invoice_return_detail->hpp) }}">
                                                </td>
                                                <td class="text-end align-top">
                                                    <input type="text" id="subtotal_{{ $key }}" name="subtotal[]" value="{{ formatNumber($invoice_return_detail->subtotal) }}" class="form-control text-end" readonly>
                                                </td>
                                                <td>
                                                    @foreach ($invoice_return_detail->invoice_return_taxes as $key_tax => $invoice_return_tax)
                                                        <div class="mb-1">
                                                            <input class="tax_id_{{ $key }}" type="hidden" id="tax_id_{{ $key }}_{{ $key_tax }}" name="tax_id[{{ $invoice_return_detail->reference_id }}][]" value="{{ $invoice_return_tax->tax_id }}" data-index="{{ $key_tax }}">
                                                            <input class="tax_value_{{ $key }}" type="hidden" id="tax_value_{{ $key }}_{{ $key_tax }}" name="tax_value[{{ $invoice_return_detail->reference_id }}][]" value="{{ $invoice_return_tax->value }}" data-index="{{ $key_tax }}">
                                                            <div class="row">
                                                                <div class="col-md-12">
                                                                    {{ $invoice_return_tax->tax->name }} {{ formatNumber($invoice_return_tax->value * 100) }}%
                                                                </div>
                                                                <div class="col-md-12">
                                                                    <input class="text-end form-control tax_amount_{{ $key }}" type="text" id="tax_amount_{{ $key }}_{{ $key_tax }}" name="tax_amount[{{ $invoice_return_detail->reference_id }}][]" value="{{ formatNumber($invoice_return_tax->amount) }}" readonly data-index="{{ $key_tax }}">
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    <input type="hidden" id="subtotal_tax_amount_{{ $key }}" name="subtotal_tax_amount[]" value="{{ formatNumber($invoice_return_detail->tax_amount) }}" class="form-control text-end" readonly>
                                                </td>
                                                <td class="text-end align-top">
                                                    <input type="text" id="total_{{ $key }}" name="total[]" value="{{ formatNumber($invoice_return_detail->total) }}" class="form-control text-end" readonly>
                                                </td>
                                                <td>
                                                    <button type="button" onclick="$('#row-{{ $key }}').remove();calculateTotal()" class="btn btn-sm btn-danger"><i class="fa fa-minus"></i></button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4"></th>
                                            <th class="text-end">TOTAL</th>
                                            <th class="text-end" id="grand_subtotal_text">{{ formatNumber($model->subtotal) }}</th>
                                            <th class="text-end" id="grand_tax_amount_text">{{ formatNumber($model->tax_total) }}</th>
                                            <th class="text-end" id="grand_total_text">{{ formatNumber($model->total) }}</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                            <x-button type="submit" color="primary" label="update data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/invoice-return/transaction.js') }}?v=1"></script>

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#trading');
        sidebarActive('#invoice-return')
        checkClosingPeriod($('#date'));

        $(document).ready(function() {
            initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
                id: "id",
                text: "code,name"
            }, 2, {
                branch_id: function() {
                    return $('#branch_id').val();
                }
            });

            initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
                id: "id",
                text: "nama"
            });

            initSelect2Search(`reference_parent_id`, `{{ route('admin.select.invoice-return-do-select') }}`, {
                id: "id",
                text: "code",
            }, 0, {
                branch_id: function() {
                    return $('#branch_id').val()
                },
                customer_id: function() {
                    return $('#customer_id').val()
                }
            });
        })
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
