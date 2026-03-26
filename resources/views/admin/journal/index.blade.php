@extends('layouts.admin.layout.index')

@php
    $main = 'journal';
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

                <div class="row justify-content-end" id="generatedFilter">
                    <div class="col-md-2">
                        <x-select name="type" id="journal-generated-type" class="form-control">
                            <option value="">Pilih Type</option>
                            @foreach ($journalTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="journal-generated-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="journal-generated-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-1 row align-self-end">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="journal-generated-type-submit" size="sm" icon="search" fontawesome />
                        </div>
                    </div>
                </div>

                <div class="row justify-content-end" id="createdFilter">
                    <div class="col-md-2">
                        <x-select name="type" id="journal-created-type" class="form-control">
                            <option value="">Pilih Type</option>
                            @foreach ($journalTypes as $type)
                                <option value="{{ $type }}">{{ $type }}</option>
                            @endforeach
                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="from_date" label="from date" value="" id="journal-created-from-date" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="to" label="to date" value="" id="journal-created-to-date" required />
                        </div>
                    </div>
                    <div class="col-md-1 row align-self-end">
                        <div class="form-group">
                            <x-button type="submit" color="primary" id="journal-created-type-submit" size="sm" icon="search" fontawesome />
                        </div>
                    </div>
                </div>

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#generated-tab" id="generated-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">Generated</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link rounded " data-bs-toggle="tab" href="#created-tab" id="created-btn" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">General</span>
                        </a>
                    </li>
                </ul>

                <div class="tab-content mt-30">
                    <div class="tab-pane active" id="generated-tab" role="tabpanel">
                        <x-table id="generated">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th>{{ Str::headline('reference') }}</th>
                                <th>{{ Str::headline('type') }}</th>
                                <th>{{ Str::headline('total') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('Last Modified') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </div>
                    <div class="tab-pane " id="created-tab" role="tabpanel">
                        <x-table id="created">
                            <x-slot name="table_head">
                                <th>{{ Str::headline('#') }}</th>
                                <th>{{ Str::headline('kode') }}</th>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <th>{{ Str::headline('reference') }}</th>
                                <th>{{ Str::headline('type') }}</th>
                                <th>{{ Str::headline('total') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                                <th>{{ Str::headline('Last Modified') }}</th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">

                            </x-slot>
                        </x-table>
                    </div>
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    @can("view $main")
        <script>
            $(document).ready(() => {

                const setGenatedJounal = () => {
                    const table = $('table#generated').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.data") }}',
                            data: {
                                is_generated: 1,
                                type: $('#journal-generated-type').val(),
                                from_date: function() {
                                    return $('#journal-generated-from-date').val();
                                },
                                to_date: function() {
                                    return $('#journal-generated-to-date').val();
                                },
                            }
                        },
                        order: [
                            [1, 'desc'],
                            [2, 'desc']
                        ],
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
                                data: 'date',
                                name: 'date'
                            },
                            {
                                data: 'reference',
                                name: 'reference'
                            },
                            {
                                data: 'journal_type',
                                name: 'journal_type'
                            },
                            {
                                data: 'credit_total',
                                name: 'credit_total'
                            },
                            {
                                data: 'status',
                                name: 'status'
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
                    $('table#generated').css('width', '100%');
                }



                const setCreatedJounal = () => {
                    const table = $('table#created').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        destroy: true,
                        ajax: {
                            url: '{{ route("admin.$main.data") }}',
                            data: {
                                is_generated: 0,
                                type: $('#journal-created-type').val(),
                                from_date: $('#journal-created-from-date').val(),
                                to_date: $('#journal-created-to-date').val(),
                            }
                        },
                        order: [
                            [1, 'desc'],
                            [2, 'desc']
                        ],
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
                                data: 'date',
                                name: 'date'
                            },
                            {
                                data: 'reference',
                                name: 'reference'
                            },
                            {
                                data: 'journal_type',
                                name: 'journal_type'
                            },
                            {
                                data: 'credit_total',
                                name: 'credit_total'
                            },
                            {
                                data: 'status',
                                name: 'status'
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
                    $('table#created').css('width', '100%');
                }

                $('#journal-generated-type-submit').click(function(e) {
                    e.preventDefault()
                    setGenatedJounal()
                })

                $('#journal-created-type-submit').click(function(e) {
                    e.preventDefault()
                    setCreatedJounal()
                })


                $('#generated-btn').click(function(e) {
                    e.preventDefault();
                    $('#generatedFilter').show()
                    $('#createdFilter').hide()
                    setGenatedJounal();
                });

                $('#created-btn').click(function(e) {
                    e.preventDefault();
                    $('#generatedFilter').hide()
                    $('#createdFilter').show()
                    setCreatedJounal();
                });

                $('#createdFilter').hide()
                setGenatedJounal();
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#journal')
    </script>
@endsection
