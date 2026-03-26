@extends('layouts.admin.layout.index')

@php
    $main = 'unit';
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
            @can("create $main")
                <div class="row justify-content-between mb-4">
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" />
                        <x-modal title="create new data" id="create-modal" headerColor="info">
                            <x-slot name="modal_body">
                                <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    <div class="form-group">
                                        <x-input type="text" label="nama" name="name" label="nama" required />
                                    </div>
                                    <div class="form-group">
                                        <x-input type="text" label="sort" name="sort" label="sort" required />
                                    </div>
                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                    <x-button type="submit" color="primary" label="Save data" />
                                </form>
                            </x-slot>
                        </x-modal>
                    </div>
                </div>
            @endcan
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")
                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline(Str::headline('nama')) }}</th>
                        <th>{{ Str::headline(Str::headline('sort')) }}</th>
                        <th>{{ Str::headline('Created At') }}</th>
                        <th>{{ Str::headline('Last Modified At') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table').DataTable({
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
                            data: 'name',
                            name: 'name'
                        },

                        {
                            data: 'sort',
                            name: 'sort'
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
        sidebarActive('#unit')
    </script>
@endsection
