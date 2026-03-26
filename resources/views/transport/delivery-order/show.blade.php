@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
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
                        <a href="{{ route("transport.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail purchase transport') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    <x-card-data-table title="{{ 'detail ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table theadColor='danger'>
                <x-slot name="table_head">
                    <th></th>
                    <th></th>
                </x-slot>
                <x-slot name="table_body">
                    <tr>
                        <th>{{ Str::headline('kode') }}</th>
                        <td>{{ $model->kode }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('vendor') }}</th>
                        <td>{{ $model->vendor->nama }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('harga') }}</th>
                        <td>{{ $model->currency->simbol }} {{ formatNumber($model->harga) }} / {{ $model->so_trading->so_trading_detail->item->unit->name ?? '' }}</td>
                    </tr>
                    @foreach ($model->so_trading->sh_number->sh_number_details as $item)
                        <tr>
                            <th>{{ Str::headline($item->type) }}</th>
                            <td>{{ $item->alamat }}</td>
                        </tr>
                    @endforeach

                    @if ($model->is_have_any_to_request_print)
                        <tr>
                            <th>Request Print all</th>
                            <td>
                                <x-button color="success" icon="check" fontawesome label="request print all" size="sm" dataToggle="modal" dataTarget="#print-modal" />
                                <x-modal title="print purchase order" id="print-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("transport.$main.show.request-print-all", $model) }}' method="post">
                                            @method('PUT')
                                            @csrf

                                            <input type="hidden" name="status" value="request-print">
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <th>{{ Str::headline('tanggal') }}</th>
                        <td>{{ $model->created_at->format('d-m-Y') }}</td>
                    </tr>
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>

    <x-card-data-table title="{{ 'list ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            <x-table theadColor="" id="list-delivery-orders">
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('Nomor do') }}</th>
                    <th>{{ Str::headline('target delivery') }}</th>
                    <th>{{ Str::headline('tanggal muat') }}</th>
                    <th>{{ Str::headline('tanggal bongkar') }}</th>
                    <th>{{ Str::headline('kuantitas dikirim') }}</th>
                    <th>{{ Str::headline('kuantitas diterima') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th></th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>

    <script>
        $('#list-delivery-orders').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '{{ route("transport.$main.show.list", $model) }}',
                data: {
                    kuantitas_kirim: '{{ $item->kuantitas_kirim }}'
                }
            },
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'code',
                    name: 'code'
                },
                {
                    data: 'target_delivery',
                    name: 'target_delivery'
                },
                {
                    data: 'load_date',
                    name: 'load_date'
                },
                {
                    data: 'unload_date',
                    name: 'unload_date'
                },
                {
                    data: 'load_quantity',
                    name: 'load_quantity'
                },
                {
                    data: 'unload_quantity_realization',
                    name: 'unload_quantity_realization'
                },
                {
                    data: 'status',
                    name: 'status'
                },
                {
                    data: 'action',
                    name: 'action'
                },
            ]
        });
        $('#list-delivery-orders').css('width', '100%');
        sidebarActive('#transport-delivery-order')
    </script>
@endsection
