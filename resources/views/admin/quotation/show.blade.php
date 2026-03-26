@extends('layouts.admin.layout.index')

@php
    $main = 'quotation';
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
    <div>
        <div class="box text-white" style="background-image: linear-gradient(90deg, rgb(255, 111, 0), rgb(24, 0, 84))">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        <h4 class="m-0">Detail Purchase Request</h4>
                        <h1 class="m-0">{{ $model->code }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                {{-- <h5 class="text-center">{{ Str::headline('status_purchase_request') }}</h5>
                                <div
                                    class="badge badge-lg badge-{{ purchase_request_status()[$model->status]['color'] }}">
                                    {{ Str::headline(purchase_request_status()[$model->status]['label']) }}
                                </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <x-card-data-table title="{{ 'detail ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('kode') }}</th>
                                <td>{{ $model->code }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('customer') }}</th>
                                <td>{{ $model->customer->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('currency') }}</th>
                                <td>{{ $model->currency->kode }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('exchange rate') }}</th>
                                <td>{{ formatNumber($model->exchange_rate) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Total') }}</th>
                                <td>
                                    <span>{{ $model->currency->simbol }} &nbsp;</span>
                                    <span>{{ formatNumber($model->total) }}</span>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('created_at') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('last_modified') }}</th>
                                <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                        <x-button color="primary" label="Generate SO" icon="plus" link="{{ route('admin.sales-order.create') }}" fontawesome size="sm" />
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="Items">
                <x-slot name="header_content">
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                        </div>
                    </div>
                </x-slot>
                <x-slot name="table_content">
                    <x-table>
                        <x-slot name="table_head">
                            <th>{{ Str::upper('#') }}</th>
                            <th>{{ Str::upper('item') }}</th>
                            <th>{{ Str::upper('qty') }}</th>
                            <th>{{ Str::upper('harga') }}</th>
                            <th>{{ Str::upper('subtotal') }}</th>
                            <th>{{ Str::upper('pajak') }}</th>
                            <th>{{ Str::upper('total') }}</th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->quotationItems as $item)
                                <tr>
                                    <td>{{ $loop->index + 1 }}</td>
                                    <td>{{ $item->item->nama }} - {{ $item->item->kode }}</td>
                                    <td>{{ formatNumber($item->quantity) }} {{ $item->item->unit->name }}</td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $model->currency->simbol }}&nbsp;</span>
                                            <span> {{ formatNumber($item->price) }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $model->currency->simbol }}&nbsp;</span>
                                            <span> {{ formatNumber($item->sub_total) }}</span>
                                        </div>
                                    </td>
                                    <td class="text-end">
                                        <table width="100%">
                                            <tbody>
                                                @foreach ($item->itemTax as $itemTax)
                                                    <tr>
                                                        <td class="text-start">{{ $itemTax->tax->name }} - {{ formatNumber($itemTax->value * 100) }}%</td>
                                                        <td class="text-end">{{ formatNumber($itemTax->total) }}</td>
                                                    </tr>
                                                @endforeach

                                            </tbody>
                                        </table>
                                    </td>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $model->currency->simbol }}&nbsp;</span>
                                            <span> {{ formatNumber($item->sub_total_after_tax) }}</span>
                                        </div>

                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <th>
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->quotationItems->sum('sub_total')) }}</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->quotationItems->sum('sub_total_after_tax') - $model->quotationItems->sum('sub_total')) }}</span>
                                    </div>
                                </th>
                                <th>
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->total) }}</span>
                                    </div>
                                </th>
                            </tr>
                            <tr class="bg-dark">
                                <td colspan="7"></td>
                            </tr>
                            <tr>
                                <th class="text-end" colspan="6">Total Item</th>
                                <th class="text-end">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->sub_total_after_tax) }}</span>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-end" colspan="6">Total Item Additional</th>
                                <th class="text-end">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->additional_sub_total_after_tax) }}</span>
                                    </div>
                                </th>
                            </tr>
                            <tr>
                                <th class="text-end" colspan="6">Grand Total</th>
                                <th class="text-end">
                                    <div class="d-flex justify-content-between">
                                        <span>{{ $model->currency->simbol }} &nbsp;</span>
                                        <span>{{ formatNumber($model->total) }}</span>
                                    </div>
                                </th>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            $.ajax({
                type: "get",
                url: "{{ route('admin.quotation-add-on-type.detail') }}",
                // data: "",
                // dataType: "dataType",
                success: function({
                    data
                }) {
                    let html = ``;
                    data.map((e, key) => {
                        html += `
                                <option value="${e.id}">${e.nama}</option>
                            `;
                    });
                    $('#quotation_add_on_type_id').html(html);
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#quotation')
    </script>
@endsection
