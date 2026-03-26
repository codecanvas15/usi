@extends('layouts.admin.layout.index')

@php
    $main = 'project';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-model">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-model active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-model active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")

        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>
                                        {{ $model->code }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('cabang') }}</label>
                                    <p>
                                        {{ $model->branch->name ?? '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>
                                        {{ localDate($model->date) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('atas_nama') }}</label>
                                    <p>
                                        {{ $model->on_behalf_of }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nama') }}</label>
                                    <p>
                                        {{ $model->name }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dekripsi') }}</label>
                                    <p>
                                        {{ $model->description }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <br>
                                    <div class="badge badge-lg badge-{{ project_status()[$model->status]['color'] }}">
                                        {{ project_status()[$model->status]['label'] }} -
                                        {{ project_status()[$model->status]['text'] }}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal selesai') }}</label>
                                    <p>
                                        {{ $model->date_end ? localDate($model->date_end) : '-' }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('file') }}</label>
                                    @if ($model->file)
                                        <p>
                                            <a href="{{ asset('storage/' . $model->file) }}" target="_blank">
                                                <i class="fas fa-file-pdf"></i> Lihat File
                                            </a>
                                        </p>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('created_at') }}</label>
                                    <p>
                                        {{ toDayDateTimeString($model->created_at) }}
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('last modified') }}</label>
                                    <p>
                                        {{ toDayDateTimeString($model->updated_at) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}

                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if (in_array($model->status, ['pending', 'revert']))
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                            @endif

                            @if (in_array($model->status, ['pending']))
                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">

                        @if (in_array($model->status, ['active', 'inactive']))
                            @if ($model->status == 'active')
                                @can("deactivate $main")
                                    <x-button color="warning" icon="x" fontawesome label="deactivate" size="sm" dataToggle="modal" dataTarget="#deactivate-modal-{{ $model->id }}" />
                                    <x-modal title="deactivate project" id="deactivate-modal-{{ $model->id }}" headerColor="warning">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="deactivate">
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

                            @if ($model->status == 'inactive')
                                @can("activate $main")
                                    <x-button color="success" icon="check" fontawesome label="activate" size="sm" dataToggle="modal" dataTarget="#activate-modal-{{ $model->id }}" />
                                    <x-modal title="activate project" id="activate-modal-{{ $model->id }}" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="activate">
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

                            @if (in_array($model->status, ['active', 'inactive']))
                                @can("cancel $main")
                                    <x-button color="danger" icon="x" fontawesome label="cancel" size="sm" dataToggle="modal" dataTarget="#cancel-modal-{{ $model->id }}" />
                                    <x-modal title="cancel project" id="cancel-modal-{{ $model->id }}" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="cancel">
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

                                @can("close $main")
                                    <x-button color="success" icon="x" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal-{{ $model->id }}" />
                                    <x-modal title="close project" id="close-modal-{{ $model->id }}" headerColor="success">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="close">
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
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
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#project-sidebar');
    </script>
@endsection
