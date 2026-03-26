@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $menu = 'aktiva tetap';
@endphp

@section('title', Str::headline("Create Master $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('master') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
        @csrf

        <x-card-data-table title="{{ 'tambah ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                <div class="row">
                    <div class="form-group">
                        <label for="asset_code">Nomor Asset</label>
                        <h3>{{ $code }}</h3>
                    </div>
                </div>

                <div class="row">

                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="branch_id" id="branch_id" label="Branch" required>
                                <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="item_category_id" id="item_category_id" label="Kategori" required autofocus>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="asset_category_id" id="asset_category_id_select" label="kategori asset" required>
                                <option value="">------------</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="asset_name" name="asset_name" label="nama asset" required value="" />
                        </div>
                        <div class="form-group">
                            <x-input class="datepicker-input" id="purchase_date" name="purchase_date" label="tanggal pembelian" required value="{{ date('d-m-Y') }}" />
                        </div>
                        <div class="form-group">
                            <x-input class="datepicker-input" id="usage_date" name="usage_date" label="tanggal pemakaian" required value="{{ date('d-m-Y') }}" onchange="calculateDepreciationValue('month')" />
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="depreciation_method" id="depreciation_method" label="metode depresiasi" required autofocus>
                                <option value="straight_line_method">Straight Line Method</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="asset_coa_id" id="asset_coa_id" class="coa_select" label="Akun Aset" required autofocus>

                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="acumulated_depreciation_coa_id" id="acumulated_depreciation_coa_id" class="coa_select" label="Akun Akumulasi Depresiasi" required autofocus>

                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="depreciation_coa_id" id="depreciation_coa_id" class="coa_select" label="Akun Depresiasi" required autofocus>

                            </x-select>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" id="value" name="value" label="nilai perolehan" class="text-end commas-form" required value="" onkeyup="calculate_depreciated_value()" />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="residual_value" name="residual_value" class="text-end commas-form" label="nilai residu" required value="" onkeyup="calculate_depreciated_value()" />
                        </div>
                        <input type="hidden" id="depreciated_value" name="depreciated_value" required value="" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="depreciation_percentage" class="text-end commas-form" name="depreciation_percentage" label="persentase depresiasi" required onkeyup="calculateDepreciationValue('percent')" value="0" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="estimated_life" class="text-end" name="estimated_life" label="umur depresiasi (bulan)" required onkeyup="calculateDepreciationValue('month')" value="0" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="depreciation_value" class="text-end" label="Nilai depresiasi /bulan" required readonly value="" />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="depreciation_end_date" name="depreciation_end_date" label="tanggal akhir depresiasi" required value="" readonly />
                        </div>
                    </div>

                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="division_id" id="division_id" label="Divisi" required autofocus>

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="initial_location" name="initial_location" label="Lokasi Awal Aset" required value="" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" id="is_fleet" name="is_fleet" class="filled-in chk-col-primary" value="1" onclick="$('#form-vehicle-type').toggleClass('d-none')">
                                    <label for="is_fleet">Aset adalah armada</label>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group d-none" id="form-vehicle-type">
                                    <x-select name="vehicle_type" id="vehicle_type" label="Jenis Armada" required autofocus>
                                        <option value="darat">Darat</option>
                                        <option value="laut">Laut</option>
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <div class="row" id="row-asset-document">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="checkbox" id="asset_document" name="asset_document" class="filled-in chk-col-primary" value="1">
                                    <label for="asset_document">Aset Dokument</label>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <div id="content-table-asset-document">

                </div>

                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-text-area name="note" label="note" id="note" cols="30" rows="10"></x-text-area>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                    <x-button type="submit" color="primary" label="Save data" />
                </div>

            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/asset/index.js') }}"></script>
    <script src="{{ asset('js/admin/select/division.js') }}"></script>
    <script src="{{ asset('js/admin/select/item_category.js') }}"></script>
    <script src="{{ asset('js/admin/select/assetCategory.js') }}"></script>
    <script src="{{ asset('js/admin/select/assetDocumentType.js') }}"></script>

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
    </script>

    <script>
        initCoaSelect('.coa_select');
        initDivisionSelect('#division_id');
        initItemCategorySelect('#item_category_id');
        initAssetCategorySelect('#asset_category_id_select');

        $('#asset_category_id_select').on('change', function() {
            let data = $("#asset_category_id_select").select2('data')[0];
            $('#depreciation_percentage').val(formatRupiahWithDecimal(data.percentage ?? 0));

            setTimeout(() => {
                calculateDepreciationValue('percent');
            }, 1000);
        });
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif

@endsection
