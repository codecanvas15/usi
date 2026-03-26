@extends('layouts.admin.layout.index')

@php
    $main = 'user';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <x-card-data-table title="{{ 'edit ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route("admin.$main.update", $model) }}" method="post">
                    @csrf
                    @method('PUT')
                    <div class="row mt-20" id="user-base">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Tipe User</label>
                                <p>{{ Str::headline($model->user_type) }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="username" value="{{ $model->username }}" name="username" id="" readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" label="name" value="{{ $model->name }}" name="name" id="" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="email" label="email" value="{{ $model->email }}" name="email" id="" required />
                            </div>
                        </div>
                    </div>
                    @if ($model->user_type == 'non-employee')
                        <div class="row mt-20">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="gender" name="gender" label="jenis kelamin" required>
                                        <option value="Laki-laki" {{ $model->nonEmployee->gender == 'Laki-laki' ? 'selected' : '' }}>Laki - laki</option>
                                        <option value="Perempuan" {{ $model->nonEmployee->gender == 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="no. telepon" name="phone" id="" required value="{{ $model->nonEmployee->phone }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="instansi" name="agency" id="" required value="{{ $model->nonEmployee->agency }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="alamat instansi" name="address" id="" required value="{{ $model->nonEmployee->address }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="nomor KTP" name="identity_number" id="" required value="{{ $model->nonEmployee->identity_number }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" label="jabatan" name="role_name" id="" required value="{{ $model->nonEmployee->role }}" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select label="role" name="role[]" id="role-select" required multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ $model->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($model->user_type == 'employee')
                        <div class="row mt-20">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="branch_id" name="branch_id" label="branch">
                                        @if ($model && $model->branch)
                                            <option value="{{ $model->branch_id }}" selected>{{ $model->branch?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="division_id" name="division_id" label="division">
                                        @if ($model && $model->division)
                                            <option value="{{ $model->division_id }}">{{ $model->division?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select id="project_id" name="project_id" label="project">
                                        @if ($model && $model->project)
                                            <option value="{{ $model->project_id }}">{{ $model->project?->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-select label="role" name="role[]" id="role-select" required multiple>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}" {{ $model->hasRole($role->name) ? 'selected' : '' }}>{{ $role->name }}</option>
                                        @endforeach
                                    </x-select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_id" label="pegawai" id="employee-select" required>
                                    @if ($model->employee)
                                        <option value="{{ $model->employee_id }}" selected>{{ $model->employee?->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    @endif

                    @if ($model->user_type == 'vendor')
                        <div class="row mt-20">
                            <div class="col-md-4">
                                <x-select name="vendor_id" id="vendor-id" label="vendor" disabled>
                                    <option value="{{ $model->user_vendor->id }}">{{ $model->user_vendor->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                    @endif

                    <div class="row mt-20">
                        @if (Auth::user()->hasRole('super_admin'))
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="password" name="password" label="new_password" id="" />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="password" name="password_confirmation" label="password_confirmation" id="" />
                                </div>
                            </div>
                        @endif

                    </div>

                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#user-sidebar');
    </script>

    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#role-select').select2();

            initSelect2Search('branch_id', "{{ route('admin.select.branch') }}", {
                id: "id",
                text: "name"
            });

            initSelect2Search('division_id', "{{ route('admin.select.division') }}", {
                id: "id",
                text: "name"
            });

            initSelect2Search('project_id', "{{ route('admin.select.project') }}", {
                id: "id",
                text: "name"
            });

            initSelect2Search(`employee-select`, "{{ route('admin.select.employee') }}", {
                id: "id",
                text: "name"
            })
        });
    </script>
@endsection
