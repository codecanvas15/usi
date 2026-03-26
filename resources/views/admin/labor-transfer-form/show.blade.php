@extends('layouts.admin.layout.index')

@php
    $main = 'labor-transfer-form';
    $title = 'Formulir Pemindahan Tenaga Kerja';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'detail ' . $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('Tanggal') }}</th>
                                <td>{{ Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('kode') }}</th>
                                <td>{{ $model->reference }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('branch') }}</th>
                                <td>{{ ucwords($model->branch->name) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('karyawan') }}</th>
                                <td>
                                    <a href="{{ route('admin.employee.show', $model->employee->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->employee->name)) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('diajukan oleh') }}</th>
                                <td>
                                    <a href="{{ route('admin.employee.show', $model->submitted_by_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->submitted_by_data->name)) }}</a>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dari pt') }}</th>
                                <td>{{ ucwords(strtolower($model->from_company)) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('ke pt') }}</th>
                                <td>{{ ucwords(strtolower($model->to_company)) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dari cabang') }}</th>
                                <td>{{ $model->from_branch_data->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('ke cabang') }}</th>
                                <td>{{ $model->to_branch_data->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('dari dep./bagian') }}</th>
                                <td>{{ $model->from_division_data->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('ke dep./bagian') }}</th>
                                <td>{{ $model->to_division_data->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('alasan') }}</th>
                                <td>{{ $model->reason }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('mengetahui') }}</th>
                                <td>
                                    <a href="{{ route('admin.employee.show', $model->created_by_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->created_by_data->name)) }}</a>
                                </td>
                            </tr>
                            @if ($model->approval_status == 'approve')
                                <tr>
                                    <th>{{ Str::headline('menyetujui') }}</th>
                                    <td>{{ $model->approved_by_data->name }}</td>
                                </tr>
                            @endif
                            <tr>
                                <th>{{ Str::headline('approval status') }}</th>
                                <td>
                                    @if ($model->approval_status == 'pending' or $model->approval_status == 'revert')
                                        @if ($model->approval_status == 'pending')
                                            <span class="badge badge-warning">Pending - waiting approval</span>
                                        @endif
                                    @elseif ($model->approval_status == 'approve')
                                        <span class="badge badge-info">Approve - Formulir Pemindahan Tenaga Kerja has been
                                            approved.</span>
                                    @else
                                        <span class="badge badge-dark">Reject - Formulir Pemindahan Tenaga Kerja
                                            rejected.</span>
                                    @endif
                                </td>
                            </tr>
                        </x-slot>
                        <x-slot name="footer">
                            <div class="d-flex justify-content-end gap-1">
                                <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                                @if ($model->check_available_date)
                                    @if ($model->status == 'pending' or $model->status == 'revert')
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
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-3">
            {!! $authorization_log_view !!}
            <div class="row">
                <div class="col-md-12">
                    <x-card-data-table title="{{ 'Action' }}">
                        <x-slot name="table_content">
                            @if ($model->check_available_date)
                                @can("approve $main")
                                    <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal" />
                                    <x-modal title="approve labor-transfer-form" id="approve-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="approve">
                                                <input type="hidden" name="employee_id" value="{{ $model->employee_id }}">
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                                @can("reject $main")
                                    <x-button color="dark" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#reject-modal" />
                                    <x-modal title="reject labor-transfer-form" id="reject-modal" headerColor="dark">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update_status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="reject">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        </x-slot>
                    </x-card-data-table>
                    <x-card-data-table title="{{ 'Export' }}">
                        <x-slot name="table_content">
                            <x-button color='info' fontawesome label="export" icon="file-pdf" target="_blank" class="w-auto" size="sm" link='{{ route('labor-transfer-form.export', ['id' => encryptId($model->id)]) }}' onclick="show_print_out_modal(event)" />
                        </x-slot>
                    </x-card-data-table>
                    <x-card-data-table title="{{ 'Status Logs' }}">
                        <x-slot name="table_content">
                            <ul class="list-group">
                                @forelse ($status_logs as $item)
                                    <li class="list-group-item">
                                        @if ($item->from_status && $item->to_status)
                                            <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                                {{ Str::headline($item->to_status) }}</h5>
                                        @elseif (!$item->from_status && $item->to_status)
                                            <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                        @endif
                                        <p class="mb-0">{{ Str::title($item->message) }}</p>
                                        <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                            {{ toDayDateTimeString($item->created_at) }}</small>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <h5 class="fw-bold">Empty</h5>
                                    </li>
                                @endforelse
                            </ul>
                        </x-slot>
                    </x-card-data-table>
                    <x-card-data-table title="{{ 'Data Log' }}">
                        <x-slot name="table_content">
                            <ul class="list-group">
                                @forelse ($activity_logs as $item)
                                    <li class="list-group-item">
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                        <p class="mb-0">{{ Str::title($item->description) }}</p>
                                        <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                            {{ toDayDateTimeString($item->created_at) }}</small>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <h5 class="fw-bold">Empty</h5>
                                    </li>
                                @endforelse
                            </ul>
                        </x-slot>
                    </x-card-data-table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#contract-sidebar');
        sidebarActive('#labor-transfer-form')
    </script>
@endsection
