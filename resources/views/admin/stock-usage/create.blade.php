@extends('layouts.admin.layout.index')

@php
    $main = 'stock-usage';
@endphp

@section('title', Str::headline("tambah $main") . ' - ')

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
                        {{ Str::headline('Tambah ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.stock-usage.store') }}" method="post">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                <input type="hidden" name="type" value="{{ request()->get('type') }}">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" label="tanggal" name="date" id="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required onchange="checkClosingPeriod($(this))" />
                        </div>
                    </div>
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="branch-selectForm" required>
                                    <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="division_id" label="divisi" id="employee-division-selectForm" required></x-select>
                        </div>
                    </div>
                </div>

                {{-- EMPLOYEE --}}
                @if (request()->get('type') == 'pegawai')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_id" label="karyawan" id="employee-selectForm" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="gudang" id="warehouse-selectForm" required></x-select>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- END EMPLOYEE --}}

                {{-- FLEET --}}
                @if (request()->get('type') == 'kendaraan')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="fleet_type" label="tipe kendaraan" id="fleetType-selectFrom" required>
                                    <option value="" selected>Pilih Item</option>
                                    <option value="darat">Darat</option>
                                    <option value="laut">Laut</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="fleet_id" label="kendaraan" id="fleet-selectForm" required></x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="gudang" id="warehouse-selectForm" required></x-select>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- END FLEET --}}

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group" id="project-select-div">
                            <x-select name="project_id" label="project" id="project-selectForm"></x-select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" id="purchase-request-select-div">
                            <x-select name="purchase_request_id[]" label="purchase-request" id="purchase-request-selectForm" multiple></x-select>
                        </div>
                    </div>
                </div>

            </x-slot>

        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div id="dataStockUsage-details">
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <x-input type="text" label="note" name="note" id="" label="note" required />
                    </div>
                </div>
                <div class="d-flex justify-content-end">
                    <x-button color='primary' label="simpan" type="submit" />
                </div>
            </x-slot>
        </x-card-data-table>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/select/division.js') }}"></script>
    <script src="{{ asset('js/admin/select/employee.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>
    <script src="{{ asset('js/admin/select/vehicleSelect.js') }}"></script>
    <script src="{{ asset('js/admin/select/warehouse.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/project.js') }}"></script>

    <script>
        var item_data = [];
        $(document).ready(function() {
            checkClosingPeriod($('#date'));

            initSelect2SearchPagination(`coa-selectForm`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: ["Other Expense", "Expense"]
            });

            initSelect2SearchPagination(`purchase-request-selectForm`, `{{ route('admin.select.purchase-request-global') }}`, {
                id: "id",
                text: "kode"
            }, 0, {
                'must_filter_project': true,
                'project_id': function() {
                    return $('#project-selectForm').val();
                }
            });
        });

        initDivisionSelect('#employee-division-selectForm');
    </script>

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch-selectForm`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif

    @if (request()->get('type') == 'pegawai')
        <script>
            $(document).ready(function() {
                initSelectEmployee('#employee-selectForm');
            });
        </script>
    @endif

    @if (request()->get('type') == 'kendaraan')
        <script>
            $(document).ready(function() {
                $('#fleetType-selectFrom').change(function(e) {
                    e.preventDefault();
                    if (this.value) {
                        initVehicleSelect2Search('fleet-selectForm', `{{ route('admin.select.fleet.type') }}/${this.value}`);
                    }
                });
            });
        </script>
    @endif

    @include('admin.stock-usage.partial.form')

    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#stock-usage');
    </script>
@endsection
