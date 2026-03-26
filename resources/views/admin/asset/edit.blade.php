@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $document_main = 'asset-document';
    $menu = 'aktiva tetap';
@endphp

@section('title', Str::headline("Edit Master $main") . ' - ')

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
                        {{ Str::headline('edit ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')

    <x-card-data-table title="{{ 'edit ' . $menu }}">
        <x-slot name="table_content">
            @include('components.validate-error')
            <form id="form-edit" action="{{ route("admin.$main.update", ['asset' => $model->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="form-group">
                        <label for="asset_code">Nomor Asset</label>
                        <h3>{{ $model->code }}</h3>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="branch_id" id="branch_id" label="Branch" required>
                                <option value="{{ $model->branch_id }}">{{ $model->branch->name }}</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="item_category_name" name="item_category_name" label="kategori" value="{{ $model->item_category?->nama }}" readonly />
                            <input type="hidden" name="item_category_id" value="{{ $model->item_category_id }}" readonly />
                        </div>
                        <div class="form-group">
                            <x-select name="asset_category_id" id="asset_category_id_select" label="kategori asset" required>
                                @if ($model->asset_category)
                                    <option value="{{ $model->asset_category_id }}">{{ $model->asset_category->name }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="asset_name" name="asset_name" label="nama asset" required value="{{ $model->asset_name }}" />
                        </div>
                        <div class="form-group">
                            @if ($model->item_receiving_report_detail)
                                <x-input class="datepicker-input" id="purchase_date" name="purchase_date" label="tanggal pembelian" required value="{{ localDate($model->purchase_date ?? $model->item_receiving_report_detail->item_receiving_report->date_receive) }}" readonly />
                            @else
                                <x-input class="datepicker-input" id="purchase_date" name="purchase_date" label="tanggal pembelian" required value="{{ localDate($model->purchase_date) }}" />
                            @endif
                        </div>
                        <div class="form-group">
                            <x-input class="datepicker-input" id="usage_date" name="usage_date" label="tanggal pemakaian" required value="{{ localDate($model->usage_date ?? ($model->item_receiving_report_detail->item_receiving_report->date_receive ?? $model->purchase_date)) }}" onchange="calculateDepreciationValue('month')" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="depreciation_method" id="depreciation_method" label="metode depresiasi" required autofocus>
                                <option value="straight_line_method">Straight Line Method</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="asset_coa_id" id="asset_coa_id" class="coa_select" label="akun asset" required autofocus>
                                @if ($model->asset_coa)
                                    <option value="{{ $model->asset_coa_id }}">{{ $model->asset_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="acumulated_depreciation_coa_id" id="acumulated_depreciation_coa_id" class="coa_select" label="Akun Akumulasi Depresiasi" required autofocus>
                                @if ($model->acumulated_depreciation_coa)
                                    <option value="{{ $model->acumulated_depreciation_coa_id }}">{{ $model->acumulated_depreciation_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="depreciation_coa_id" id="depreciation_coa_id" class="coa_select" label="akun depresiasi" required autofocus>
                                @if ($model->depreciation_coa)
                                    <option value="{{ $model->depreciation_coa_id }}">{{ $model->depreciation_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" id="value" name="value" label="nilai perolehan" class="commas-form text-end" required value="{{ formatNumber($model->value) }}" onkeyup="calculate_depreciated_value()" />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="residual_value" name="residual_value" class="commas-form text-end" label="nilai residu" required value="{{ formatNumber($model->residual_value) }}" onkeyup="calculate_depreciated_value()" />
                        </div>
                        <input type="hidden" id="depreciated_value" name="depreciated_value" required value="{{ formatNumber($model->depreciated_value) }}" />
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="depreciation_percentage" class="text-end commas-form" name="depreciation_percentage" label="persentase depresiasi" required onkeyup="calculateDepreciationValue('percent')" value="{{ $model->depreciation_percentage }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" id="estimated_life" class="text-end" name="estimated_life" label="umur depresiasi (bulan)" required onkeyup="calculateDepreciationValue('month')" value="{{ $model->estimated_life }}" />
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="depreciation_value" class="text-end" label="nilai depresiasi /bulan" required readonly value="{{ formatNumber($model->depreciation_value) }}" />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="depreciation_end_date" name="depreciation_end_date" label="tanggal akhir depresiasi" required value="{{ $model->depreciation_end_date }}" readonly />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="book_value" class="text-end" label="nilai buku" required readonly value="{{ formatNumber($model->book_value) }}" />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="acumulated_depreciation" class="text-end" label="akumulasi depresiasi" required readonly value="{{ formatNumber($model->acumulated_depreciation) }}" />
                        </div>
                    </div>
                </div>
                <div class="row mt-20 border-top border-primary pt-2">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group">
                                        <input type="checkbox" id="is_fleet" name="is_fleet" class="filled-in chk-col-primary" value="1" @if ($model->is_fleet == 1) checked @endif onclick="$('#form-vehicle-type').toggleClass('d-none')">
                                        <label for="is_fleet">Aset adalah armada</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="form-group @if ($model->is_fleet != 1) d-none @endif" id="form-vehicle-type">
                                        <x-select name="vehicle_type" id="vehicle_type" label="Jenis Armada" required autofocus>
                                            <option value="darat" @if ($model->vehicle_type == 'darat') selected @endif>Darat</option>
                                            <option value="laut" @if ($model->vehicle_type == 'laut') selected @endif>Laut</option>
                                        </x-select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            @if ($model->item_receiving_report_detail)
                                <x-select name="division_id" id="division_id" label="Divisi" required autofocus disabled>
                                    @if ($model->division)
                                        <option value="{{ $model->division->id }}">{{ $model->division->name }}</option>
                                    @endif
                                </x-select>
                            @else
                                <x-select name="division_id" id="division_id" label="Divisi" required autofocus>
                                    @if ($model->division)
                                        <option value="{{ $model->division->id }}">{{ $model->division->name }}</option>
                                    @endif
                                </x-select>
                            @endif
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="initial_location" name="initial_location" label="Lokasi Awal Aset" required value="{{ $model->initial_location }}" />
                        </div>
                        <div class="form-group">
                            <x-text-area name="note" label="note" id="note" cols="30" rows="10">{!! $model->note !!}</x-text-area>
                        </div>
                    </div>
                </div>
                <div class="col-md-12 text-end">
                    <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                    @can('edit master-asset')
                        <x-button type="submit" color="primary" label="Save data" />
                    @endcan
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/ckeditor5/build/ckeditor.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/asset/index.js') }}"></script>
    <script src="{{ asset('js/admin/select/division.js') }}"></script>
    <script src="{{ asset('js/admin/select/assetCategory.js') }}"></script>
    <script src="{{ asset('js/admin/select/assetDocumentType.js') }}"></script>

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
        initAssetCategorySelect('#asset_category_id_select');

        $('#asset_category_id_select').on('change', function() {
            let data = $("#asset_category_id_select").select2('data')[0];
            $('#depreciation_percentage').val(formatRupiahWithDecimal(data.percentage ?? 0));

            setTimeout(() => {
                calculateDepreciationValue('percent');
            }, 1000);
        });
    </script>

    <script>
        initCoaSelect('.coa_select');
        initDivisionSelect('#division_id');
        initSelect2Search('branch_id', `{{ route('admin.select.branch') }}`, {
            id: "id",
            text: "name"
        });

        if ('{{ $model->item_id }}') {
            $('#asset_coa_id').select2('destroy');
        }
    </script>

@endsection
