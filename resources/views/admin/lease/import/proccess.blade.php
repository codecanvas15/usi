@extends('layouts.admin.layout.index')

@php
    $main = 'lease';
    $menu = 'Preview import biaya dibayar dimuka';
@endphp

@section('title', Str::headline($menu) . ' - ')

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
                        {{ Str::headline('Biaya Dibayar dimuka') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $menu }}">
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.lease.store-import') }}" method="post" enctype="multipart/form-data">
                @csrf
                @foreach ($data as $item)
                    <div class="border-bottom border-primary py-20">
                        <div class="row">
                            <div class="col-md-3">
                                @isset($item['lease_data'])
                                    <span class="badge badge-success">Already Exists</span>
                                @else
                                    <span class="badge badge-danger">Not Exists</span>
                                @endisset
                                <div class="form-group">
                                    <x-input type="text" value="{{ $item['lease_name'] }}" label="Nama BDM" name="lease_name[]" required="required"></x-input>
                                    <input type="hidden" name="lease_id[]" value="{{ $item['lease_data']->id ?? '' }}">
                                    <input type="hidden" name="month_duration[]" value="{{ $item['month_duration'] }}">
                                    <input type="hidden" name="depreciation_value[]" value="{{ $item['depreciation_value'] }}">
                                    <input type="hidden" name="counter[]" value="{{ $item['counter'] }}">
                                    <input type="hidden" name="acumulated_depreciation_value[]" value="{{ $item['acumulated_depreciation_value'] }}">
                                    <input type="hidden" name="last_depreciation_date[]" value="{{ $item['last_depreciation_date'] }}">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="select-branch-{{ $loop->index }}" name="branch_id[]" label="branch" required>
                                        @if ($item['branch_id'])
                                            <option value="{{ $item['branch_id'] }}" selected>{{ $item['branch_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="select-division-{{ $loop->index }}" name="division_id[]" label="Divisi">
                                        @if ($item['division_id'])
                                            <option value="{{ $item['division_id'] }}" selected>{{ $item['division_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="select-item-{{ $loop->index }}" name="item_id[]" label="Item">
                                        @if ($item['item_id'])
                                            <option value="{{ $item['item_id'] }}" selected>{{ $item['item_data']->nama }} - {{ $item['item_data']->kode }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="assetCoa-select-{{ $loop->index }}" label="asset coa" name="asset_coa_id[]" required>
                                        @if ($item['asset_coa_id'])
                                            <option value="{{ $item['asset_coa_id'] }}" selected>{{ $item['asset_coa_data']->account_code }} - {{ $item['asset_coa_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="accumulateDepreciationCoa-select-{{ $loop->index }}" label="acumulate depreciation coa" name="acumulated_depreciation_coa_id[]" required>
                                        @if ($item['acumulated_depreciation_coa_id'])
                                            <option value="{{ $item['acumulated_depreciation_coa_id'] }}" selected>{{ $item['acumulated_depreciation_coa_data']->account_code }} - {{ $item['asset_coa_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select id="DepreciationCoa-select-{{ $loop->index }}" label="depreciation_coa" name="depreciation_coa_id[]" required>
                                        @if ($item['depreciation_coa_id'])
                                            <option value="{{ $item['depreciation_coa_id'] }}" selected>{{ $item['depreciation_coa_data']->account_code }} - {{ $item['asset_coa_data']->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input class="datepicker-input" value="{{ $item['date'] }}" label="tanggal pembelian" name="date[]" required="required"></x-input>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input class="datepicker-input" value="{{ $item['from_date'] }}" label="tanggal mulai" name="from_date[]" required="required"></x-input>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input class="datepicker-input" value="{{ $item['to_date'] }}" label="tanggal berakhir" name="to_date[]" required="required"></x-input>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" class="commas-form" value="{{ formatNumber($item['value']) }}" label="nilai" name="value[]" required="required"></x-input>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="tezt" value="{{ $item['note'] }}" label="note" name="note[]" required="required"></x-input>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="status[]" label="Status" required="required">
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </x-select>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="mt-10">
                    <x-button type="submit" color="primary" icon="save" label="Simpan" />
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/select/branch.js') }}"></script>
    <script src="{{ asset('js/admin/select/division.js') }}"></script>
    <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>

    @foreach ($data as $item)
        <script>
            $(document).ready(function() {

                initBranchSelect('#select-branch-{{ $loop->index }}');
                initDivisionSelect('#select-division-{{ $loop->index }}');
                inititemSelect('select-item-{{ $loop->index }}');
                initCoaSelect('#assetCoa-select-{{ $loop->index }}');
                initCoaSelect('#accumulateDepreciationCoa-select-{{ $loop->index }}');
                initCoaSelect('#DepreciationCoa-select-{{ $loop->index }}');
            });
        </script>
    @endforeach

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#lease-sidebar');
    </script>
@endsection
