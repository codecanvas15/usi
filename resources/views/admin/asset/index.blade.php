@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $menu = 'aktiva tetap';
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
                        {{ Str::headline('master') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('master ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="Master {{ $menu }}">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                <div class="col-md-3 col-md-6 col-xl-4">
                    @can('create master-asset')
                        <x-button color="info" icon="plus" label="Tambah" link='{{ route("admin.$main.create") }}' />
                        <x-button color="info" icon="download" label="Import" link='{{ route("admin.$main.import") }}' />
                    @endcan
                </div>
            </div>
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

            <x-table id="asset_table">
                <x-slot name="table_head">
                    <th>#</th>
                    <th>{{ Str::headline('no.') }}</th>
                    <th>{{ Str::headline('nama aset') }}</th>
                    <th>{{ Str::headline('tgl purchase') }}</th>
                    <th>{{ Str::headline('kategori') }}</th>
                    <th>{{ Str::headline('nilai') }}</th>
                    <th>{{ Str::headline('sisa nilai') }}</th>
                    <th>{{ Str::headline('armada') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        var csrf = $('input[name="_token"]').val();
        const setTable = () => {
            initSelect2Search('branch-select', '{{ route('admin.select.branch') }}', {
                'id': 'id',
                'text': 'name'
            });
            $("table#asset_table").DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                order: [
                    [1, "desc"]
                ],
                ajax: {
                    url: `${base_url}/asset-datatable`,
                    type: 'POST',
                    data: {
                        _token: csrf,
                        branch_id: $('#branch-select').val()
                    }
                },
                columns: [{
                        data: "DT_RowIndex",
                        name: "DT_RowIndex",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "code",
                        name: "code",
                    },
                    {
                        data: "asset_name",
                    },
                    {
                        data: "purchase_date",
                    },
                    {
                        data: "item_category_name",
                        name: "item_categories.nama",
                    },
                    {
                        data: "value",
                    },
                    {
                        data: "outstanding_value",
                        name: "value",
                    },
                    {
                        data: "is_fleet",
                        name: "is_fleet",
                        orderable: false,
                        searchable: false,
                    },
                    {
                        data: "status",
                    },
                    {
                        data: "action",
                        name: "action",
                        orderable: false,
                        searchable: false,
                    },
                ],
            });
            $("table").css("width", "100%");
        }

        $(document).ready(() => {
            setTable();
        });

        $('#set-table').click(function(e) {
            e.preventDefault();
            setTable();
        });
    </script>
    {{-- <script src="{{ asset('js/admin/asset/datatable.js') }}"></script> --}}
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
    </script>
@endsection
