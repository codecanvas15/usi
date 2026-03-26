@extends('layouts.admin.layout.index')

@php
    $main = 'currency';
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
                            <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" />
                            <x-modal title="create new data" id="create-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <div class="form-group">
                                            <x-input type="text" label="nama" name="nama" required />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="kode" name="kode" required />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="simbol" name="simbol" />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="remark" name="remark" required />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="negara" name="negara" required />
                                        </div>
                                        {{-- <div class="form-group">
                                            <x-input type="text" label="kurs" name="exchange_rate" required />
                                        </div> --}}
                                        <div class="form-group">
                                            <x-input-checkbox label="active" checked name="active" id="checkbox-1" value="1" />
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

                <x-table id="currency_table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('Nama') }}</th>
                        <th>{{ Str::upper('kode') }}</th>
                        <th>{{ Str::upper('Simbol') }}</th>
                        <th>{{ Str::upper('remark') }}</th>
                        <th>{{ Str::upper('negara') }}</th>
                        <th>{{ Str::upper('local currency') }}</th>
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
                const table = $('table#currency_table').DataTable({
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
                            data: 'nama',
                            name: 'nama'
                        },
                        {
                            data: 'kode',
                            name: 'kode'
                        },
                        {
                            data: 'simbol',
                            name: 'simbol'
                        },
                        {
                            data: 'remark',
                            name: 'remark'
                        },
                        {
                            data: 'negara',
                            name: 'negara'
                        },
                        {
                            data: 'is_local',
                            name: 'is_local'
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
        sidebarActive('#currency')
    </script>
@endsection
