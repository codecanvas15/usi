@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah minat $title") . ' - ')

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
                        {{ Str::headline('tambah minat ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.employee.update.step4', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="50" aria-valuemin="0" aria-valuemax="100" style="width: 50%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'minat ' . $title }}">
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <h5>Masukkan rangkin dari 1 sampai 10</h5>
                    @foreach ($model->employeeInterests as $item)
                        <div class="row mt-10">

                            <div class="col-md-4">
                                <div class="form-group d-flex align-self-center">
                                    <label for="" class="text-end">{{ $item->interest }}</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="interest[]" value="{{ $item->interest }}">
                                    <x-input name="rank[]" type="number" :value="$item->rank" label="-" hideAsterix required />
                                </div>
                            </div>
                        </div>
                    @endforeach

                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.create.step5', ['employee_id' => $model->id]) }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#employee-sidebar');
        $('body').addClass('sidebar-collapse');
    </script>
@endsection
