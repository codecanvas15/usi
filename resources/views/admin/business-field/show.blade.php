@extends('layouts.admin.layout.index')

@php
    $main = 'business-field';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
    @can("view $main")
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
                            <th>{{ Str::headline('name') }}</th>
                            <td>{{ $model->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('created_at') }}</th>
                            <td>{{ toDayDateTimeString($model->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('last medified') }}</th>
                            <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan

                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="List vendor">
            <x-slot name="table_content">
                <x-table id="vendor-data">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('kode') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
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
    @can("view $main")
        <script>
            $(document).ready(() => {

                const initTable = () => {
                    const table = $('table#vendor-data').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route('admin.business-field.vendor', $model) }}',
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
                                data: 'business_field',
                                name: 'business_field'
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
        sidebarActive('#business-field-sidebar');
    </script>
@endsection
