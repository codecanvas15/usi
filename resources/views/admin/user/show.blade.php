@extends('layouts.admin.layout.index')

@php
    $main = 'user';
    $title = 'pengguna';
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
    @can('view user')
        <x-card-data-table title="{{ 'detail ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('tipe user') }}</label>
                            <p>
                                {{ Str::headline($model->user_type) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('username') }}</label>
                            <p>
                                {{ $model->username }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('nama') }}</label>
                            <p>
                                {{ $model->name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('email') }}</label>
                            <p>
                                {{ $model->email }}
                            </p>
                        </div>
                    </div>
                    @if ($model->user_type == 'vendor')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('vendor') }}</label>
                                <p>
                                    {{ $model->user_vendor->nama ?? '' }}
                                </p>
                            </div>
                        </div>
                    @endif
                    @if ($model->user_type == 'employee')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('branch') }}</label>
                                <p>
                                    {{ $model->branch?->name }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('division') }}</label>
                                <p>
                                    {{ $model->division?->name }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('pegawai') }}</label>
                                <p>
                                    {{ $model->employee?->name }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('role') }}</label>
                                <p>
                                    {{ Str::headline(implode($model->getRoleNames()->toArray())) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    @if ($model->user_type == 'non-employee')
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('jenis kelamin') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->gender }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('no. telepon') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->phone }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('instansi') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->agency }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('alamat') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->address }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('no. KTP') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->identity_number }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('jabatan') }}</label>
                                <p>
                                    {{ $model->nonEmployee?->role }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="">{{ Str::headline('role') }}</label>
                                <p>
                                    {{ Str::headline(implode($model->getRoleNames()->toArray())) }}
                                </p>
                            </div>
                        </div>
                    @endif

                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('created_at') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('updated_at') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan

                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#user-sidebar')
    </script>
@endsection
