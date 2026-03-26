@extends('layouts.admin.layout.index')

@php
    $main = 'permission-letter-employee';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@php
    $types = [
        'came too late' => 'Izin datang terlambat',
        'leave during working hours' => 'Izin keluar kantor pada jam kerja',
        'leave early' => 'Izin pulang lebih awal',
        'not work' => 'Izin tidak masuk kerja',
    ];
    if ($model->letter_type == 'came too late') {
        $jam_datang = date('H:i:s', strtotime($model->letter_date_start));
        $tanggal = date('d-m-Y', strtotime($model->letter_date_start));
    }
    if ($model->letter_type == 'leave early') {
        $jam_datang = date('H:i:s', strtotime($model->letter_date_end));
        $tanggal = date('d-m-Y', strtotime($model->letter_date_end));
    }
    if ($model->letter_type == 'leave during working hours') {
        $jam_datang = date('H:i:s', strtotime($model->letter_date_start));
        $jam_pulang = date('H:i:s', strtotime($model->letter_date_end));
        $tanggal = date('d-m-Y', strtotime($model->letter_date_start));
    }
    if ($model->letter_type == 'not work') {
        $tanggal = date('d-m-Y', strtotime($model->letter_date_start));
    }
@endphp

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'detail ' . $main }}">
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
                                    <th>{{ Str::headline('Nomor Surat') }}</th>
                                    <td>{{ $model->letter_number }}</td>
                                </tr>
                                @if ($model->letter_type == 'came too late')
                                    <tr>
                                        <th>{{ Str::headline('Tanggal') }}</th>
                                        <td>{{ localDate($tanggal) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ Str::headline('Jam Datang') }}</th>
                                        <td>{{ $jam_datang }}</td>
                                    </tr>
                                @endif

                                @if ($model->letter_type == 'leave early')
                                    <tr>
                                        <th>{{ Str::headline('Tanggal') }}</th>
                                        <td>{{ localDate($tanggal) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ Str::headline('Jam Pulang') }}</th>
                                        <td>{{ $jam_datang }}</td>
                                    </tr>
                                @endif

                                @if ($model->letter_type == 'leave during working hours')
                                    <tr>
                                        <th>{{ Str::headline('Tanggal') }}</th>
                                        <td>{{ localDate($tanggal) }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ Str::headline('Jam Datang') }}</th>
                                        <td>{{ $jam_datang }}</td>
                                    </tr>

                                    <tr>
                                        <th>{{ Str::headline('Jam Pulang') }}</th>
                                        <td>{{ $jam_pulang }}</td>
                                    </tr>
                                @endif

                                @if ($model->letter_type == 'not work')
                                    <tr>
                                        <th>{{ Str::headline('Tanggal') }}</th>
                                        <td>{{ localDate($tanggal) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>{{ Str::headline('type') }}</th>
                                    <td>{{ $types[$model->letter_type] }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('Karyawan') }}</th>
                                    <td>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('Alasan') }}</th>
                                    <td>{{ $model->letter_reason }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('Note') }}</th>
                                    <td>{{ $model->letter_note }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('File') }}</th>
                                    <td>
                                        @if ($model->file)
                                            <a href='{{ asset('storage/' . $model->file) }}' class="w-auto btn btn-sm btn-primary" target="_blank">
                                                <i class="fa-solid fa-download"></i>
                                            </a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>
                                        {{ Str::headline('status') }}
                                    </th>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="badge badge-lg badge-{{ purchase_request_status()[$model->letter_status]['color'] }}">
                                                {{ purchase_request_status()[$model->letter_status]['label'] }} -
                                                {{ purchase_request_status()[$model->letter_status]['text'] }}
                                            </div>

                                            @php
                                                $type = 'permission-letter-employee';

                                            @endphp
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_at') }}</th>
                                    <td>{{ toDayDateTimeString($model->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('last medified') }}</th>
                                    <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->letter_status == 'pending' or $model->letter_status == 'revert')
                                @if ($model->check_available_date)
                                    @can("edit $type")
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    @endcan

                                    @can("delete $type")
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
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @if ($model->check_available_date)
                            @if ($model->letter_status == 'pending' or $model->letter_status == 'revert')
                                @can("approve $type")
                                    <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal" />
                                    <x-modal title="approve purchase request" id="approve-modal" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="approve">

                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan

                                @if ($model->letter_status == 'pending' or $model->letter_status == 'partial-approve')
                                    @can("reject $type")
                                        <x-button color="dark" icon="x" fontawesome label="reject all" size="sm" dataToggle="modal" dataTarget="#reject-modal" />
                                        <x-modal title="reject purchase request" id="reject-modal" headerColor="dark">
                                            <x-slot name="modal_body">
                                                <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
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
                            @endif

                            @if ($model->letter_status == 'approve')
                                @can("revert $type")
                                    <x-button color="dark" icon="x" fontawesome label="revert" size="sm" dataToggle="modal" dataTarget="#revert-modal" />
                                    <x-modal title="revert purchase request" id="revert-modal" headerColor="dark">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="revert">
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
                                @can("void $type")
                                    <x-button color="danger" icon="trash" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
                                    <x-modal title="void purchase request" id="void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <div class="mt-10">
                                                    <div class="form-group">
                                                        <x-input type="text" id="message" label="message" name="message" required />
                                                    </div>
                                                </div>
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="cancel" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                        @endif
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarMenuOpen('#permission-letter-employee');
    </script>
@endsection
