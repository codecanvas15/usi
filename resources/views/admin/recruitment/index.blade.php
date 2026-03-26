@extends('layouts.admin.layout.index')

@php
    $main = 'cuti';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    @can("view $main")
        <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    @endcan
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
                    <div class="col-md-4">
                        @can("create $main")
                            <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                        @endcan
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table>
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('Nama') }}</th>
                        <th>{{ Str::upper('Email') }}</th>
                        <th>{{ Str::upper('Nomor Telpon') }}</th>
                        <th>{{ Str::upper('Deskripsi Job') }}</th>
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
                            data: null,
                            render: function(data) {
                                var html = [];
                                console.log(data)
                                html.push(data.name);

                                return html.join('<br>');
                            },
                            name: 'nama'
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'no_telp',
                            name: 'no_telp'
                        },
                        {
                            data: 'deskripsi_job',
                            name: 'deskripsi_job'
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
        sidebarMenuOpen('#hrd');
        sidebarActive('#cuti')
    </script>
@endsection
