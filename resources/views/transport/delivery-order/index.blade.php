@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view transport-$main")
                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('kode') }}</th>
                        <th>{{ Str::upper('reference') }}</th>
                        <th>{{ Str::upper('Customer') }}</th>
                        <th>{{ Str::upper('delivery selesai') }}</th>
                        <th>{{ Str::upper('status') }}</th>
                        <th>{{ Str::upper('Created At') }}</th>
                        <th>{{ Str::upper('Last Modified At') }}</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view transport-$main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("transport.$main.index") }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'so_trading.nomor_so',
                            name: 'so_trading.nomor_so'
                        },
                        {
                            data: 'so_trading.customer.nama',
                            name: 'so_trading.customer.nama'
                        },
                        {
                            data: 'do_done',
                            name: 'do_done'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarActive('#transport-delivery-order')
    </script>
@endsection
