@extends('layouts.admin.layout.index')

@php
    $main = 'item-category';
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
    @can("view $main")
        <x-card-data-table title="{{ $main }}">
            <x-slot name="header_content">
                @can("create $main")
                    <div class="row justify-content-between mb-4">
                        <div class="col-md-3 col-md-6 col-xl-4">
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                            <x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />

                            <x-modal title="import" id="import-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <div class="mt-30">
                                        <form action='{{ route("admin.$main.import") }}' method="post" enctype="multipart/form-data">
                                            @csrf
                                            <div class="form-group">
                                                <x-input type="file" label="file" name="file" required />
                                            </div>
                                            <x-button color="info" icon="download" label="import" />
                                        </form>
                                    </div>

                                </x-slot>
                            </x-modal>
                        </div>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="item_category_table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper(Str::headline('kode')) }}</th>
                        <th>{{ Str::upper(Str::headline('nama')) }}</th>
                        <th>{{ Str::upper(Str::headline('item_type')) }}</th>
                        <th>{{ Str::upper('Created At') }}</th>
                        <th>{{ Str::upper('Last Modified At') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')

    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    @can("view $main")
        <script>
            $(document).ready(() => {

                const table = $('table#item_category_table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route("admin.$main.index") }}',
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
                            data: 'nama',
                            name: 'nama'
                        },

                        {
                            data: 'item_type.nama',
                            name: 'item_type.nama'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item-category')
    </script>
@endsection
