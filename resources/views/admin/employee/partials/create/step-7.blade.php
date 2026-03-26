@extends('layouts.admin.layout.index')

@php
    $main = 'employee';
    $title = 'karyawan';
@endphp

@section('title', Str::headline("tambah bank $title") . ' - ')

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
                        {{ Str::headline('tambah bank ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route('admin.employee.store.step7', ['employee_id' => $model->id]) }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="progress">
                        <div class="progress-bar progress-bar-primary progress-bar-striped" role="progressbar" aria-valuenow="87.5" aria-valuemin="0" aria-valuemax="100" style="width: 87.5%"></div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'bank ' . $title }}">
                <x-slot name="header_content">
                    <x-button color="primary" icon="plus" fontawesome="" label="Tambah bank" id="add-bank"></x-button>
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="mt-20" id="bank-content">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="lewati" link="{{ route('admin.employee.show', $model->id) }}" />
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
            const handleBank = () => {
                let bankIndex = 0;

                const addBank = (index) => {
                    let html = `
                        <div class="row pt-20 border-top border-primary" id="bank-item-${index}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_name[]" label="nama bank" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_behalf_of[]" label="atas nama" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input name="bank_account_number[]" label="nomor rekening" required />
                                </div>
                            </div>
                            <div class="col-md-3 d-flex align-self-end">
                                <div class="form-group">
                                    <x-button color="danger" icon="trash" fontawesome="fas" label="Hapus" id="remove-bank-${index}"></x-button>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#bank-content').append(html);

                    $(`#remove-bank-${index}`).click(function(e) {
                        e.preventDefault();
                        removeBank(index);
                    });
                };

                const removeBank = (index) => {
                    $(`#bank-item-${index}`).remove();
                };

                $('#add-bank').click(function(e) {
                    e.preventDefault();
                    addBank(bankIndex);
                    bankIndex++;
                });
            };

            const init = () => {
                handleBank();
            };

            init();
        });
    </script>
@endsection
