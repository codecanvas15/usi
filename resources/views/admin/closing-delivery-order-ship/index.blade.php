@extends('layouts.admin.layout.index')

@section('title', Str::headline($title) . ' - ')

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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table :title="$title">
        <x-slot name="header_content">
            @can("create $permissionName")
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("$routeNamePrefix.create") }}' />
                    </div>
                </div>
            @endcan
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $permissionName")
                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Kode') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('kode delivery order') }}</th>
                        <th>{{ Str::headline('item') }}</th>
                        <th>{{ Str::headline('Hilang') }}</th>
                        <th>{{ Str::headline('Nilai Hilang') }}</th>
                        <th>{{ Str::headline('Status') }}</th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $permissionName")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("$routeNamePrefix.index") }}',
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
                            data: 'date',
                            name: 'date'
                        },
                        {
                            name: 'delivery_order_id',
                            data: 'delivery_order.code'
                        },
                        {
                            name: 'item_id',
                            data: 'item.nama'
                        },
                        {
                            data: 'losses_quantity',
                            name: 'losses_quantity'
                        },
                        {
                            data: 'amount_losses',
                            name: 'amount_losses'
                        },
                        {
                            data: 'status',
                            name: 'status'
                        }
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#closing-delivery-order-ship');
    </script>
@endsection
