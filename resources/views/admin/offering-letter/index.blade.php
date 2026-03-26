@extends('layouts.admin.layout.index')

@php
    $main = 'offering-letter';
    $title = 'Letter of Intent';
@endphp

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
                        HRD
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            @can("create $main")
                <div class="row mb-4">
                    <div class="col-md-2 col-xl-2">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                    </div>
                </div>
            @endcan
            <div class="row align-items-end" id="stock_transfer_receiving">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="user-assesment-from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="user-assesment-to-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-button type="submit" color="primary" id="set-user-assesment-table" icon="search" fontawesome />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table id="offering-letter">
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('Kode') }}</th>
                    <th>{{ Str::headline('Karyawan') }}</th>
                    <th>{{ Str::headline('Dibuat Pada') }}</th>
                    <th>{{ Str::headline('Diperbarui Pada') }}</th>
                    <th>Aksi</th>
                    <th>Export</th>
                    <th>Tanggapan Pelamar</th>
                </x-slot>
                <x-slot name="table_body"></x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const offeringLetterTable = () => {
                const table = $('table#offering-letter').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#user-assesment-from-date').val(),
                            to_date: $('#user-assesment-to-date').val(),
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'reference',
                            name: 'reference'
                        },
                        {
                            data: 'name',
                            name: 'name'
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
                            "data": "action"
                        },
                        {
                            data: 'export',
                            name: 'export'
                        },
                        {
                            data: 'copy_link',
                            name: 'copy_link',
                            orderable: false,
                            searchable: false,
                        }
                    ]
                });
                $('table').css('width', '100%');
            }

            offeringLetterTable();

            $('#set-user-assesment-table').click(function(e) {
                e.preventDefault();
                offeringLetterTable();
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#offering-letter')
    </script>
@endsection
