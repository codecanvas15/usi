@extends('layouts.admin.layout.index')

@php
    $main = 'hrd-assessment';
    $title = 'Interview HRD';
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
                    <div class="col-md-3 col-xl-4">
                        <x-button link='{{ route("admin.$main.download") }}' color="info" icon="download" label="form HRD assessment" />
                    </div>
                </div>
            @endcan
            <div class="row align-items-end" id="stock_transfer_receiving">
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="from_date" label="from date" value="" id="hrd-assessment-from-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" name="to" label="to date" value="" id="hrd-assessment-to-date" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-button type="submit" color="primary" id="set-hrd-assessment-table" icon="search" fontawesome />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <x-table id="hrd-assessment">
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('Kode') }}</th>
                    <th>{{ Str::headline('Tanggal') }}</th>
                    <th>{{ Str::headline('Kandidat') }}</th>
                    <th>{{ Str::headline('Interviewer') }}</th>
                    <th>{{ Str::headline('Status') }}</th>
                    <th>{{ Str::headline('Approval') }}</th>
                    <th>{{ Str::headline('Export') }}</th>
                    <th>Aksi</th>
                </x-slot>
                <x-slot name="table_body"></x-slot>
            </x-table>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const hrdAssessmentTable = () => {
                const table = $('table#hrd-assessment').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            from_date: $('#hrd-assessment-from-date').val(),
                            to_date: $('#hrd-assessment-to-date').val(),
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
                            data: 'assessment_date',
                            name: 'assessment_date'
                        },
                        {
                            data: 'candidate_data',
                            name: 'candidate_data'
                        },
                        {
                            data: 'interviewer_data',
                            name: 'interviewer_data'
                        },
                        {
                            data: 'assessment_status',
                            name: 'assessment_status'
                        },
                        {
                            data: 'approval_status',
                            name: 'approval_status'
                        },
                        {
                            "data": "action"
                        },
                        {
                            "data": "export"
                        },
                    ]
                });
                $('table').css('width', '100%');
            }

            hrdAssessmentTable()

            $('#set-hrd-assessment-table').click(function(e) {
                e.preventDefault();
                hrdAssessmentTable()
            })
        });
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#hrd-assessment')
    </script>
@endsection
