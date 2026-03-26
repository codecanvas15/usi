@extends('layouts.admin.layout.index')

@php
    $main = 'ware-house';
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
                        </div>
                    </div>
                @endcan
                @if (get_current_branch()->is_primary)
                    <div class="row mb-15">
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-select id="branch-select" label="branch">

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-1 row align-self-end">
                            <div class="form-group">
                                <x-button type="submit" color="primary" id="set-table" icon="search" fontawesome />
                            </div>
                        </div>
                    </div>
                @endif
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="ware_house_table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('ID') }}</th>
                        <th>{{ Str::upper('Type') }}</th>
                        <th>{{ Str::upper('Nama') }}</th>
                        <th>{{ Str::upper('alamat') }}</th>
                        <th>{{ Str::upper('kota') }}</th>
                        <th>{{ Str::upper('provinsi') }}</th>
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
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script>
            const setTable = () => {
                initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
                    'id': 'id',
                    'text': 'name'
                });
                $('table#ware_house_table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            branch_id: $('#branch-select').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'id',
                            name: 'id',
                            searchable: false
                        },
                        {
                            data: 'type',
                            name: 'type'
                        },
                        {
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'jalan',
                            name: 'jalan'
                        },
                        {
                            data: 'kota',
                            name: 'kota'
                        },
                        {
                            data: 'provinsi',
                            name: 'provinsi'
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
            }
            $(document).ready(() => {
                setTable();
            });

            $('#set-table').click(function(e) {
                e.preventDefault();
                setTable();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#ware-house')
    </script>
@endsection
