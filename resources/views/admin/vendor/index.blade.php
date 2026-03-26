@extends('layouts.admin.layout.index')

@php
    $main = 'vendor';
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
                <div class="row justify-content-between mb-4">
                    <div class="col-md-12">
                        @can("create $main")
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        @endcan
                        @can("export $main")
                            <x-button link='{{ route("admin.$main.export") }}' color="info" icon="upload" label="export" />
                        @endcan
                        @can("import $main")
                            <x-button link='{{ route("admin.$main.import") }}' color="info" icon="download" label="import" dataToggle="modal" dataTarget="#import-modal" />

                            <x-modal title="import" id="import-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <x-button link='{{ route("admin.$main.import-format") }}' color="info" icon="download" label="import format" />

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
                        @endcan
                        @can("import $main")
                            <x-button color="dark" icon="download" label="Saldo Awal" link="{{ route('admin.vendor-debt.create') }}" />
                        @endcan
                    </div>
                </div>

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row mb-10">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="" label="bidang usaha" id="select-bussiness-field">

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="info" icon="search" fontawesome size="sm" id="btn-datatable" />
                        </div>
                    </div>
                </div>

                <x-table id="vendor_table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('Tipe') }}</th>
                        <th>{{ Str::headline('Bidang Usaha') }}</th>
                        <th>{{ Str::headline('email') }}</th>
                        <th>{{ Str::headline('bussiness phone') }}</th>
                        <th>{{ Str::headline('mobile phone') }}</th>
                        <th>{{ Str::headline('whatapps') }}</th>
                        <th>{{ Str::headline('fax') }}</th>
                        <th>{{ Str::headline('Last Modified At') }}</th>
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
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    @can("view $main")
        <script>
            $(document).ready(() => {

                initSelect2Search('select-bussiness-field', '{{ route('admin.select.business-field') }}', {
                    id: "id",
                    text: "name"
                });

                const initTable = () => {
                    const table = $('table#vendor_table').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.index") }}',
                            data: {
                                business_field: $('#select-bussiness-field').val(),
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
                                data: 'nama',
                                name: 'nama'
                            },
                            {
                                data: 'type',
                                name: 'type'
                            },
                            {
                                data: 'business_field',
                                name: 'business_fields.name'
                            },
                            {
                                data: 'email',
                                name: 'email'
                            },
                            {
                                data: 'business_phone',
                                name: 'business_phone'
                            },
                            {
                                data: 'mobile_phone',
                                name: 'mobile_phone'
                            },
                            {
                                data: 'whatsapp',
                                name: 'whatsapp'
                            },
                            {
                                data: 'fax',
                                name: 'fax'
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
                };

                $('table').css('width', '100%');

                $('#btn-datatable').click(function(e) {
                    e.preventDefault();
                    initTable();
                });

                initTable();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-vendor-sidebar');
        sidebarActive('#vendor-sidebar')
    </script>
@endsection
