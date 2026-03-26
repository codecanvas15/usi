@extends('layouts.admin.layout.index')

@php
    $main = 'mass-leave';
    $title = 'cuti-bersama';
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
                        <a href="{{ route('admin.' . $main . '.index') }}">{{ $title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        Detail {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">

        <div class="col-md-8">
            <x-card-data-table title="detail {{ $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ localDate($model->date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dari') }}</th>
                                <td>{{ localDate($model->from_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('sampai') }}</th>
                                <td>{{ localDate($model->to_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('alasan/keperluan cuti') }}</th>
                                <td class="text-uppercase">{{ $model->necessary }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('keterangan') }}</th>
                                <td class="text-uppercase">{{ $model->note }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('created_at') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('last medified') }}</th>
                                <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('File') }}</th>
                                <td>
                                    @if ($model->attachment)
                                        <a href="{{ asset('storage/' . $model->attachment) }}" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-paperclip"></i> Preview</a>
                                    @else
                                        Tidak ada file
                                    @endif
                                </td>
                            </tr>
                        </x-slot>
                    </x-table>
                    <div class="row">
                        <div class="col-md-12">
                            <x-table id="employee-table">
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <th>{{ Str::headline('pegawai') }}</th>
                                    <th>{{ Str::headline('divisi') }}</th>
                                    <th>{{ Str::headline('jabatan') }}</th>
                                </x-slot>
                                <x-slot name="table_body">

                                </x-slot>
                            </x-table>

                        </div>
                    </div>
                </x-slot>

                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                        @if ($model->status == 'pending')
                            @if ($model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan

                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        @endif
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-4">
            <x-card-data-table title="{{ 'Data Log' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <ul class="list-group">
                        @foreach ($activity_logs as $item)
                            <li class="list-group-item">
                                <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                <p class="mb-0">{{ Str::title($item->description) }}</p>
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#mass-leave');

        const initTable = () => {
            const table = $('table#employee-table').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                destroy: true,
                ajax: {
                    url: '{{ route('admin.mass-leave.employee-data') }}',
                    type: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        employee_ids: JSON.parse('{{ $model->mass_leave_details()->pluck('employee_id') }}'),
                    },
                },
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'NIK',
                        name: 'employees.NIK',
                    },
                    {
                        data: 'name',
                        name: 'employees.name',
                    },
                    {
                        data: 'division_name',
                        name: 'divisions.name',
                    },
                    {
                        data: 'position_nama',
                        name: 'positions.nama',
                    },
                ]
            });

            $('table').css('width', '100%');
        };

        initTable();
    </script>
@endsection
