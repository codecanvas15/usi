@extends('layouts.admin.layout.index')

@php
    $main = 'fleet';
@endphp

@section('title', Str::headline('Edit Armada') . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline('armada') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit armada') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ !$model ? route("admin.$main.store") : route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
            <x-card-data-table title="{{ 'edit armada' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')

                    @csrf
                    @if ($model)
                        @method('PUT')
                    @endif

                    <input type="hidden" name="type" value="{{ $model->type }}">

                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="type" id="select-type" required disabled>
                                <option value="">Pilih Item</option>
                                <option value="darat" {{ $model->type == 'darat' ? 'selected' : '' }}>Darat</option>
                                <option value="laut" {{ $model->type == 'laut' ? 'selected' : '' }}>Laut</option>
                            </x-select>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_id" label="pegawai" id="employee-select">
                                    @if ($model->vechicle_fleet?->employee)
                                        <option value="{{ $model->vechicle_fleet->employee_id }}" selected>{{ $model->vechicle_fleet->employee?->name }} - {{ $model->vechicle_fleet->employee?->NIK }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                @if ($model->fleet)
                                    <x-input name="name" value="{{ $model->name }}" id="" label="Nama Armada" required autofocus readonly />
                                @else
                                    <x-input name="name" value="{{ $model->name }}" id="" label="Nama Armada" required autofocus />
                                @endif
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="merk" value="{{ $model->merk }}" id="" label="Merk" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input name="quantity" value="{{ $model->quantity }}" id="" label="Kapasitas" helpers="" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="year" name="year" value="{{ $model->year }}" id="" label="tahun_pembuatan" required />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="project_id" id="" label="Project">
                                    <option value="">Pilih Project</option>
                                    @foreach ($projects as $project)
                                        <option value="{{ $project->id }}" {{ $model->project_id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                    @endforeach
                                </x-select>
                            </div>
                        </div>
                    </div>

                    @if ($model->type == 'darat')
                        <div class="row mt-10 border-top border-primary" id="darat">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="vehicle_type" value="{{ $model->vechicle_fleet?->vehicle_type }}" id="" label="tipe kendaraan" required />
                                </div>
                            </div>
                        </div>
                    @endif
                    @if ($model->type == 'laut')
                        <div class="row mt-10 border-top border-primary" id="laut">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="nomor_lambung" value="{{ $model?->marine_fleet?->nomor_lambung }}" id="" label="nomor_lambung" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="number" name="panjang" value="{{ $model?->marine_fleet?->panjang }}" id="" label="panjang" helpers="Meter" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="number" name="lebar" value="{{ $model?->marine_fleet?->lebar }}" id="" label="lebar" helpers="Meter" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="gt" value="{{ $model?->marine_fleet?->gt }}" id="" label="gt" required />
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="box-footer">
                        <div class="d-flex justify-content-end gap-3">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {
            initSelectEmployee('#employee-select')
        });

        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-garage-sidebar');
        sidebarActive('#fleet');
    </script>
@endsection
