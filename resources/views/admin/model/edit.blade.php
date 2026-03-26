@extends('layouts.admin.layout.index')

@php
    $main = 'model';
    $title = 'master otorisasi';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

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
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <x-card-data-table title="{{ 'edit ' . $title }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.update", $model) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row align-items-end">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="alias" name="alias" label="menu" value="{{ Str::headline($model->alias) }}" required autofucus readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                @if ($model->need_to_check_amount)
                                    <x-input-checkbox name="need_to_check_amount" id="need_to_check_amount" label="check nominal" value="1" checked />
                                @else
                                    <x-input-checkbox name="need_to_check_amount" id="need_to_check_amount" label="check nominal" value="1" />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-12">
                            <p class="text-danger">* User dengan level dan nominal yang sama bersifat "atau"</p>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>{{ Str::headline('level') }}</th>
                                            <th>{{ Str::headline('branch') }}</th>
                                            <th>{{ Str::headline('divisi') }}</th>
                                            <th width="40%">{{ Str::headline('user') }}</th>
                                            <th>{{ Str::headline('nominal') }}</th>
                                            <th>{{ Str::headline('sebagai') }}</th>
                                            <th class="text-center">
                                                <button type="button" class="btn btn-primary btn-sm" id="add-authorization-list">Tambah</button>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="authorization-list">
                                        @foreach ($model->model_authorizations as $key => $model_authorization)
                                            <tr id="row_{{ $key }}">
                                                <td>
                                                    <input type="hidden" name="model_authorization_id[{{ $key }}]" id="model_authorization_id_{{ $key }}" value="{{ $model_authorization->id }}">
                                                    <input type="number" class="form-control" name="level[{{ $key }}]" id="level_{{ $key }}" required value="{{ $model_authorization->level }}">
                                                </td>
                                                <td>
                                                    <select name="branch_id[{{ $key }}][]" label="branch" id="branch_id_{{ $key }}" class="form-control form-select branch_id" multiple>
                                                        @foreach ($model_authorization->model_authorization_branches ?? [] as $model_authorization_branch)
                                                            <option value="{{ $model_authorization_branch->branch->id }}" selected>{{ $model_authorization_branch->branch->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="division_id[{{ $key }}][]" label="divisi" id="division_id_{{ $key }}" class="form-control form-select division_id" multiple>
                                                        @foreach ($model_authorization->model_authorization_divisions ?? [] as $model_authorization_division)
                                                            <option value="{{ $model_authorization_division->division->id }}" selected>{{ $model_authorization_division->division->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td>
                                                    <select name="user_id[{{ $key }}]" label="pegawai" id="user_id_{{ $key }}" required class="form-control form-select user_id">
                                                        @if ($model_authorization->user)
                                                            <option value="{{ $model_authorization->user->id }}" selected>{{ $model_authorization->user->name }} - {{ $model_authorization->user->email }} - {{ $model_authorization->user->employee->position->nama ?? 'Tidak Ada' }}</option>
                                                        @endif
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control commas-form text-end" name="minimum_value[{{ $key }}]" id="minimum_value_{{ $key }}" required value="{{ formatNumber($model_authorization->minimum_value) }}" {{ !$model->need_to_check_amount ? 'readonly' : '' }}>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control" name="role[{{ $key }}]" id="role_{{ $key }}" required value="{{ $model_authorization->role }}">
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-danger btn-sm" type="button" onclick="$('#row_{{ $key }}').remove()">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>

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
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/model/transaction.js') }}"></script>
    <script src="{{ asset('js/admin/select/user.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#model')

        $(document).ready(function() {
            initUserSelect('.user_id')
            key = '{{ $model->model_authorizations->count() }}'

            $('.division_id').each(function(index, element) {
                initSelect2Search(`division_id_${index}`, `${base_url}/select/division`, {
                    id: "id",
                    text: "name"
                }, 0, []);

                initSelect2Search(`branch_id_${index}`, `${base_url}/select/branch`, {
                    id: "id",
                    text: "name"
                }, 0, []);
            });
        })
    </script>
@endsection
