@extends('layouts.admin.layout.index')

@php
    $main = 'lease';
    $document_main = 'lease-document';
    $menu = 'biaya dibayar dimuka';
@endphp

@section('title', Str::headline("edit $menu") . ' - ')

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
    <form action="{{ route("admin.$main.update", ['lease' => $model->id]) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <x-card-data-table title="{{ 'edit ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="branch_id" id="branch_id" label="Branch" required>
                                <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="lease_name" name="lease_name" label="nama" required value="{{ $model->lease_name }}" />
                        </div>
                        <div class="form-group">
                            @if ($readonly)
                                <x-input class="datepicker-input" id="date" name="date" label="tanggal" required value="{{ localDate($model->date) }}" readonly />
                            @else
                                <x-input class="datepicker-input" id="date" name="date" label="tanggal" required value="{{ localDate($model->date) }}" />
                            @endif
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="from_date" name="from_date" label="tanggal mulai" required value="{{ localDate($model->from_date) }}" onchange="calculateDepreciationValue()" />
                        </div>
                        <div class="form-group">
                            <x-input class="datepicker-input" id="to_date" name="to_date" label="tanggal akhir" required value="{{ localDate($model->to_date) }}" onchange="calculateDepreciationValue()" />
                        </div>
                        <div class="form-group">
                            @if ($readonly)
                                <x-input type="text" id="value" name="value" label="Nilai perolehan" class="text-end commas-form" required value="{{ formatNumber($model->value) }}" readonly />
                            @else
                                <x-input type="text" id="value" name="value" label="Nilai perolehan" class="text-end commas-form" required value="{{ formatNumber($model->value) }}" />
                            @endif
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="depreciation_value" label="nilai amortisasi/bulan" class="text-end commas-form" required value="{{ formatNumber($model->depreciation_value) }}" readonly />
                        </div>
                        <div class="form-group">
                            <x-input type="text" id="book_value" name="book_value" label="nilai buku" class="text-end commas-form" required value="{{ formatNumber($model->book_value) }}" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-select name="asset_coa_id" id="asset_coa_id" class="coa_select" label="Akun" required autofocus>
                                @if ($model->asset_coa)
                                    <option value="{{ $model->asset_coa_id }}">{{ $model->asset_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="acumulated_depreciation_coa_id" id="acumulated_depreciation_coa_id" class="coa_select" label="Akun Akumulasi Amortisasi" autofocus required>
                                @if ($model->acumulated_depreciation_coa)
                                    <option value="{{ $model->acumulated_depreciation_coa_id }}">{{ $model->acumulated_depreciation_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="form-group">
                            <x-select name="depreciation_coa_id" id="depreciation_coa_id" class="coa_select" label="Akun Amortisasi" autofocus required>
                                @if ($model->depreciation_coa)
                                    <option value="{{ $model->depreciation_coa_id }}">{{ $model->depreciation_coa->name }}</option>
                                @endif
                            </x-select>
                        </div>
                    </div>
                </div>

                <div class="row mt-20 pt-20 border-top border-primary">
                    <div class="col-md-8">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    @if ($readonly)
                                        <input type="hidden" name="division_id" value="{{ $model->division_id }}">
                                        <x-input type="text" id="division" name="division" label="divisi" value="{{ $model->division->name }}" readonly />
                                    @else
                                        <x-select name="division_id" id="division_id" label="Divisi" required autofocus>
                                            @if ($model->division)
                                                <option value="{{ $model->division_id }}">{{ $model->division->name }}</option>
                                            @endif
                                        </x-select>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-text-area name="note" label="note" id="note" cols="30" rows="10">{{ $model->note }}</x-text-area>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 text-end">
                        <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                        @can("edit $main")
                            <x-button type="submit" color="primary" label="Save data" />
                        @endcan
                    </div>
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
    <script src="{{ asset('js/admin/select/division.js') }}"></script>

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#lease-sidebar');
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
    <script>
        const calculateDepreciationValue = () => {
            let value = thousandToFloat($('#value').val());
            let from_date = $('#from_date').val();
            let to_date = $('#to_date').val();

            if (value && from_date && to_date) {
                let date1 = new Date(convertLocalDate(from_date));
                let date2 = new Date(convertLocalDate(to_date));

                let diffTime = Math.abs(date2 - date1);
                let diffMonth = Math.round(diffTime / (1000 * 60 * 60 * 24 * 30));
                console.log(diffMonth);
                let depreciation_value = value / diffMonth;

                $('#depreciation_value').val(formatRupiahWithDecimal(depreciation_value));
            }
        };
    </script>
@endsection
