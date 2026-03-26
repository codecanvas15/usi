@extends('layouts.admin.layout.index')

@php
    $main = 'authorization';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        .notification-bubble {
            position: absolute;
            top: -5px;
            right: -5px;
            width: 20px;
            height: 20px;
            text-align: center;
            border-radius: 50%;
            background-color: rgb(255, 137, 137)
        }
    </style>
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
    <div class="tab-content mt-30">
        <x-card-data-table title="Otorisasi">
            <x-slot name="header_content">
                <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                    @php
                        $index = 0;
                    @endphp
                    @foreach (AUTHORIZATIONS as $key => $item)
                        <li class="nav-item">
                            <a class="nav-link rounded {{ $index == 0 ? 'active' : '' }}" data-bs-toggle="tab" href="#{{ Str::slug($key) }}-tab" id="{{ Str::slug($key) }}-btn" role="tab">
                                <span>{{ Str::headline(Str::slug($key)) }}</span>
                                <span class="notification-bubble d-none" id="notification-counter-bubble-{{ Str::slug($key) }}"></span>
                            </a>
                        </li>

                        @php
                            $index++;
                        @endphp
                    @endforeach
                </ul>
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="dari" id="fromDate-input" required value="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="" label="sampai" id="toDate-input" required value="" />
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-self-end">
                        <div class="form-group">
                            <x-button color="info" size="sm" icon="search" fontawesome id="btn-init-table" />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="table_content">
                <div class="tab-content mt-30">
                    @php
                        $index = 0;
                    @endphp
                    @foreach (AUTHORIZATIONS as $key => $item)
                        <div class="tab-pane {{ $index == 0 ? 'active' : '' }}" id="{{ Str::slug($key) }}-tab" role="tabpanel">
                            <x-table id="{{ Str::slug($key) }}-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <th>{{ Str::headline('pengajuan') }}</th>
                                    <th>{{ Str::headline('diajukan oleh') }}</th>
                                    <th>{{ Str::headline('status') }}</th>
                                    <th>{{ Str::headline('keterangan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>
                        </div>
                        @php
                            $index++;
                        @endphp
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        function get_counter() {
            $.ajax({
                type: "get",
                url: "{{ route('admin.authorization.count-each') }}",
                success: function({
                    data
                }) {
                    $.each(data, function(index, value) {
                        if (value != 0) {
                            $('#notification-counter-bubble-' + index).removeClass('d-none');
                            $('#notification-counter-bubble-' + index).html(value);
                        } else {
                            $('#notification-counter-bubble-' + index).addClass('d-none');
                        }
                    });
                }
            });
        }
        $(document).ready(function() {
            get_counter()
        });
    </script>
    <script>
        sidebarMenuOpen('#otorisasi');
    </script>
    @foreach (AUTHORIZATIONS as $key => $item)
        <script>
            $(document).ready(function() {
                $('table#{{ Str::slug($key) }}-table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route('admin.authorization.datatables') }}',
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            model: "{{ $key }}",
                            from_date: function() {
                                return $('#fromDate-input').val();
                            },
                            to_date: function() {
                                return $('#toDate-input').val();
                            },
                        },
                    },
                    order: [
                        [4, 'asc']
                    ],
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'link',
                            name: 'subtitle'
                        },
                        {
                            data: 'user_name',
                            name: 'users.name'
                        },
                        {
                            data: 'status',
                            name: 'priority',
                            searchable: false,
                        },
                        {
                            data: 'revert_or_void_necessary',
                            name: 'revert_or_void_necessary'
                        }
                    ]
                });

                $('table').css('width', '100%');

                $('#{{ $key }}-btn').click(function(e) {
                    e.preventDefault();
                    $('table#{{ Str::slug($key) }}-table').DataTable().ajax.reload();
                    get_counter()
                    get_autorization_status_pending()
                });

            });
        </script>
    @endforeach
    <script>
        $(document).ready(function() {
            $('#btn-init-table').click(function(e) {
                e.preventDefault();
                $('.tab-content .active').find('table').DataTable().ajax.reload();
                get_counter();
                get_autorization_status_pending();
            });
        });
    </script>
@endsection
