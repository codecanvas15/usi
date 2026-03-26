@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah kelebihan dan kelemahan $title") . ' - ')

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
                        {{ Str::headline('tambah kelebihan dan kelemahan ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.employee.store.step6', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="75" aria-valuemin="0" aria-valuemax="100" style="width: 75%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'kelebihan ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah kelebihan" id="add-kelebihan"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="mt-20" id="kelebihan-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'kekurangan ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah kelemahan" id="add-kekurangan"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    <div class="mt-20" id="kekurangan-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.create.step7', ['employee_id' => $model->id]) }}" />
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

    <script>
        $(document).ready(function() {

            const handleKelebihan = () => {
                let indexKelebihan = 0;

                const addKelebihan = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="kelebihan-item-${index}">
                            <input type="hidden" name="type[]" value="strength">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="description[]" label="deskripsi" required="required" />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" icon="trash" fontawesome id="delete-kelebihan-${index}" />
                                </div>
                            </div>
                        </div>`;

                    $('#kelebihan-content').append(html);
                    $(`#delete-kelebihan-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteKelebihan(index);
                    });
                };

                const deleteKelebihan = (index) => {
                    $(`#kelebihan-item-${index}`).remove();
                };

                $('#add-kelebihan').click(function(e) {
                    e.preventDefault();
                    addKelebihan(indexKelebihan);
                    indexKelebihan++;
                });
            };

            const handleKekurangan = () => {
                let indexKekurangan = 0;
                const addKekurangan = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="kekurangan-item-${index}">
                            <input type="hidden" name="type[]" value="weakness">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="description[]" label="deskripsi" required="required" />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" icon="trash" fontawesome id="delete-kekurangan-${index}" />
                                </div>
                            </div>
                        </div>`;

                    $('#kekurangan-content').append(html);
                    $(`#delete-kekurangan-${index}`).click(function(e) {
                        e.preventDefault();
                        deleteKekurangan(index);
                    });
                };

                const deleteKekurangan = (index) => {
                    $(`#kekurangan-item-${index}`).remove();
                };

                $('#add-kekurangan').click(function(e) {
                    e.preventDefault();
                    addKekurangan(indexKekurangan);
                    indexKekurangan++;
                });
            };

            const init = () => {
                handleKelebihan();
                handleKekurangan();
            };

            init();
        });
    </script>
@endsection
