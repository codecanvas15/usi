@extends('layouts.admin.layout.index')

@php
    $main = 'stock-usage';
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
    <form action="{{ route('admin.stock-usage.update', $model) }}" method="post">
        @csrf
        @method('put')
        <x-card-data-table title="{{ 'tambah ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                <input type="hidden" name="type" value="{{ $model->type }}">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" label="tanggal" name="date" id="date" value="{{ localDate($model->date) }}" required onchange="checkClosingPeriod($(this))" />
                        </div>
                    </div>
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="branch_id" label="branch" id="branch-selectForm" required>
                                    @if ($model->branch)
                                        <option value="{{ $model->branch_id }}">{{ $model->branch->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="division_id" label="divisi" id="employee-division-selectForm" required>
                                @if ($model->division)
                                    <option value="{{ $model->division_id }}">{{ $model->division->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>
                </div>

                {{-- EMPLOYEE --}}
                @if ($model->type == 'pegawai')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="employee_id" label="karyawan" id="employee-selectForm" required>
                                    @if ($model->employee)
                                        <option value="{{ $model->employee_id }}">{{ $model->employee->name }} - {{ $model->employee->NIK }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="gudang" id="warehouse-selectForm" required>
                                    @if ($model->ware_house)
                                        <option value="{{ $model->ware_house_id }}">{{ $model->ware_house->nama }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- END EMPLOYEE --}}

                {{-- FLEET --}}
                @if ($model->type == 'kendaraan')
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="fleet_type" label="tipe kendaraan" id="fleetType-selectFrom" required>
                                    <option value="" selected>Pilih Item</option>
                                    <option value="darat" {{ $model->fleet_type == 'darat' ? 'selected' : '' }}>Darat</option>
                                    <option value="laut" {{ $model->fleet_type == 'laut' ? 'selected' : '' }}>Laut</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="fleet_id" label="kendaraan" id="fleet-selectForm" required>
                                    @if ($model->fleet)
                                        <option value="{{ $model->fleet_id }}">{{ $model->fleet->name }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="gudang" id="warehouse-selectForm" required>
                                    @if ($model->ware_house)
                                        <option value="{{ $model->ware_house_id }}" selected>{{ $model->ware_house->nama }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- END FLEET --}}

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="coa_id" label="coa expense" id="coa-selectForm">
                                @if ($model->coa)
                                    <option value="{{ $model->coa_id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group" id="project-select-div">
                            <x-select name="project_id" label="project" id="project-selectForm">
                                @if ($model->project)
                                    <option value="{{ $model->project_id }}">{{ $model->project->code }} - {{ $model->project->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>


                    <div class="col-md-3">
                        <div class="form-group" id="purchase-request-select-div">
                            <x-select name="purchase_request_id[]" label="purchase-request" id="purchase-request-selectForm" multiple>
                                @foreach ($model->stock_usage_purchase_requests as $key => $stock_usage_purchase_request)
                                    <option value="{{ $stock_usage_purchase_request->purchase_request_id }}" selected>{{ $stock_usage_purchase_request->purchase_request->kode }}</option>
                                @endforeach
                            </x-select>
                        </div>
                    </div>
                </div>

            </x-slot>

        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div id="dataStockUsage-details">
                    @foreach ($model->stock_usage_details as $key => $stock_usage_detail)
                        <div class="row" id="stockUsage-card-{{ $key }}">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="item_id[]" id="item-Detail-inputForm-{{ $key }}" label="item" required autofocus>
                                        @if ($stock_usage_detail->item)
                                            <option value="{{ $stock_usage_detail->item_id }}">{{ $stock_usage_detail->item->kode }} - {{ $stock_usage_detail->item->nama }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="coa_detail_id[]" id="coa-detail-id-{{ $key }}" label="COA" required autofocus>
                                        @if ($stock_usage_detail->coa)
                                            <option value="{{ $stock_usage_detail->coa_detail_id }}">{{ $stock_usage_detail->coa->account_code }} - {{ $stock_usage_detail->coa->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="stock_left[]" id="stock-left-Detail-inputForm-{{ $key }}" value="" required="required" label="sisa stock gudang" useCustomError="true" useCustomErrorColor="primary" value="{{ formatNumber($stock_usage_detail->stock) }}" readonly />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="quantity[]" id="quantity-Detail-inputForm-{{ $key }}" class="commas-form" value="0" required="required" label="jumlah" useCustomError="true" useCustomErrorColor="primary" value="{{ formatNumber($stock_usage_detail->quantity) }}" />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="necessity[]" id="necessity-Detail-inputForm-{{ $key }}" label="keperluan" required value="{{ $stock_usage_detail->necessity }}" />
                                </div>
                            </div>
                            <div class="col-md-1 d-flex align-self-end">
                                <div class="form-group">
                                    @if ($key == 0)
                                        <x-button type="button" color="primary" id="add-data" icon="add" fontawesome size="sm" />
                                    @else
                                        <x-button type="button" color="danger" id="btn-delete-item-{{ $key }}" icon="trash" fontawesome size="sm" />
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table>
            <x-slot name="table_content">
                <div class="row justify-content-end">
                    <div class="col-md-6">
                        <x-input type="text" label="note" name="note" id="" label="note" required value="{{ $model->note }}" />
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
        $(document).ready(function() {
            checkClosingPeriod($('#date'));

            initSelect2SearchPagination(`coa-selectForm`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: "Expense"
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

    @if ('{{ $model->type }}' == 'pegawai')
        <script>
            $(document).ready(function() {
                initSelectEmployee('#employee-selectForm');
            });
        </script>
    @endif

    @if ('{{ $model->type }}' == 'kendaraan')
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

        count = '{{ $model->stock_usage_details->count() }}';

        $(document).ready(function() {
            for (let index = 0; index < count; index++) {
                $(`#btn-delete-item-${index}`).click(function(e) {
                    $(`#stockUsage-card-${index}`).remove();
                });

                initSelect2SearchPaginationData(`item-Detail-inputForm-${index}`, `{{ route('admin.select.item.type') }}/all?item_types=purchase item`, {
                    id: 'id',
                    text: 'kode,nama'
                })

                $(`#item-Detail-inputForm-${index}`).change(function(e) {
                    e.preventDefault();
                    getStockLeft(index);
                });

                initSelect2SearchPagination(`coa-detail-id-${index}`, `{{ route('admin.select.coa') }}`, {
                    id: "id",
                    text: "account_code,name"
                }, 0, {
                    account_type: ["Other Expense", "Expense"]
                });

                $(`#quantity-Detail-inputForm-${index}`).keyup(debounce(function(e) {
                    e.preventDefault();
                    let quantity = thousandToFloat($(this).val());
                    let stock_left = thousandToFloat($(`#stock-left-Detail-inputForm-${index}`).val());
                    if (quantity > stock_left) {
                        alert(`Stock tidak mencukupi, sisa stock ${stock_left}`);
                        $(this).val(0);
                    }
                }, 500));

                if (index == 0) {
                    $('#add-data').click(function(e) {
                        e.preventDefault();
                        addData(count);
                    });
                }
            }
        })
    </script>
@endsection
