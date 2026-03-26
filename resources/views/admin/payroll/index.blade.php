@extends('layouts.admin.layout.index')

@php
    $main = 'payroll';
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
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="" label="payroll period" id="payroll-period-id" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-self-end">
                            <div class="form-group">
                                <x-button color="primary" class="d-none" label="slip gaji" id="btn-slip-gaji" onclick="exportPDF()" />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="from_date" label="from date" value="" id="payroll-from-date" required />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="to" label="to date" value="" id="payroll-to-date" required />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-button type="submit" color="primary" id="set-payroll-table" icon="search" fontawesome />
                            </div>
                        </div>
                    </div>
                @endcan

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table id="payroll">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Pegawai') }}</th>
                        <th>{{ Str::headline('Periode') }}</th>
                        <th>{{ Str::headline('Tanggal') }}</th>
                        <th>{{ Str::headline('Take Home Pay') }}</th>
                        <th>{{ Str::headline('Gaji Kotor') }}</th>
                        <th>{{ Str::headline('Gaji Bersih') }}</th>
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
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>

    <script>
        var table = ''
        var period_id = $('#payroll-period-id')
        const initTable = () => {
            table = $('table#payroll').dataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                bDestroy: true,
                ajax: {
                    url: '{{ route("admin.$main.index") }}',
                    data: {
                        period_id: period_id.val(),
                        from_date: $('#payroll-from-date').val(),
                        to_date: $('#payroll-to-date').val()
                    }
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        "data": "user",
                    },
                    {
                        "data": "type",
                    },
                    {
                        "data": "date",
                    },
                    {
                        "data": "base_salary",
                    },
                    {
                        "data": "brutto_salary",
                    },
                    {
                        "data": "netto_salary",
                    },
                    {
                        "data": "action"
                    },
                ]
            });
            $('table').css('width', '100%');
        }

        initTable()

        initSelect2Search('payroll-period-id', "{{ route('admin.select.payroll-period') }}", {
            id: "id",
            text: "name"
        });

        $('#set-payroll-table').click(function(e) {
            e.preventDefault();
            initTable();
        })

        period_id.change(function() {

            if ($(this).val() != null) {
                $('#btn-slip-gaji').removeClass('d-none');
            } else {
                $('#btn-slip-gaji').addClass('d-none');

            }

            initTable()
        })

        function exportPDF() {
            var payroll_period_id = $('#payroll-period-id').val();

            window.open(`{{ route('admin.payroll.export-slip-gaji') }}?payroll_period_id=${payroll_period_id}`);
        }
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#payroll-sidebar');
        sidebarActive('#payroll')
    </script>
@endsection
