@extends('layouts.admin.layout.index')

@php
    $main = 'reset-leave';
    $title = 'periode cuti';
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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ $title }}">
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
                                            <x-input type="text" label="from_date" name="from_date" required class="datepicker-input" autocomplete="off" />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="to_date" name="to_date" required class="datepicker-input" autocomplete="off" />
                                        </div>
                                        <x-select name="status" label="status" id="status" required selectType="form-select">
                                            <option value="open">Open</option>
                                            <option value="close">Close</option>
                                        </x-select>
                                        <div class="form-group">
                                            <x-input type="text" label="note" name="note" required />
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

                <x-table id="reset-leave-table">
                    <x-slot name="table_head">
                        <th>{{ Str::upper('#') }}</th>
                        <th>{{ Str::upper('periode') }}</th>
                        <th>{{ Str::upper('status') }}</th>
                        <th>{{ Str::upper('Keterangan') }}</th>
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
                const table = $('table#reset-leave-table').DataTable({
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
                            data: 'from_date',
                            name: 'from_date'
                        },
                        {
                            data: 'note',
                            name: 'note'
                        },
                        {
                            data: 'status',
                            name: 'status'
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
        sidebarActive('#reset-leave')
    </script>
@endsection
