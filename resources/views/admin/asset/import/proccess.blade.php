@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $menu = 'Preview import aktiva tetap';
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
                        {{ Str::headline('aktiva tetap') }}
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
            <form action="{{ route('admin.asset.store-import') }}" method="post" enctype="multipart/form-data">
                @csrf
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>{{ Str::headline('item_id') }}</th>
                                <th>{{ Str::headline('asset_name') }}</th>
                                <th>{{ Str::headline('asset_category_id') }}</th>
                                <th>{{ Str::headline('branch_id') }}</th>
                                <th>{{ Str::headline('purchase_date') }}</th>
                                <th>{{ Str::headline('usage_date') }}</th>
                                <th>{{ Str::headline('asset_coa_id') }}</th>
                                <th>{{ Str::headline('acumulated_depreciation_coa_id') }}</th>
                                <th>{{ Str::headline('depreciation_coa_id') }}</th>
                                <th>{{ Str::headline('value') }}</th>
                                <th>{{ Str::headline('residual_value') }}</th>
                                <th>{{ Str::headline('depreciation_percentage') }}</th>
                                <th>{{ Str::headline('estimated_life') }}</th>
                                <th>{{ Str::headline('depreciation_value') }}</th>
                                <th>{{ Str::headline('depreciation_end_date') }}</th>
                                <th>{{ Str::headline('division_id') }}</th>
                                <th>{{ Str::headline('initial_location') }}</th>
                                <th>{{ Str::headline('note') }}</th>
                                <th>{{ Str::headline('status') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data as $item)
                                <tr>
                                    <td>
                                        <input type="hidden" value="{{ $item['item_id'] }}" name="item_id[]">
                                        <input type="hidden" value="{{ $item['acumulated_depreciation'] }}" name="acumulated_depreciation[]">
                                        <input type="hidden" value="{{ $item['last_depreciation_date'] }}" name="last_depreciation_date[]">
                                        <input type="hidden" value="{{ $item['asset_category_name'] }}" name="asset_category_name[]">
                                        {{ $item['item_data']->nama ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['asset_name'] }}" name="asset_name[]">
                                        {{ $item['asset_name'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['asset_category_id'] }}" name="asset_category_id[]">
                                        {{ $item['asset_category_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['branch_id'] }}" name="branch_id[]">
                                        {{ $item['branch_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['purchase_date'] }}" name="purchase_date[]">
                                        {{ $item['purchase_date'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['usage_date'] }}" name="usage_date[]">
                                        {{ $item['usage_date'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['asset_coa_id'] }}" name="asset_coa_id[]">
                                        {{ $item['asset_coa_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['acumulated_depreciation_coa_id'] }}" name="acumulated_depreciation_coa_id[]">
                                        {{ $item['acumulated_depreciation_coa_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['depreciation_coa_id'] }}" name="depreciation_coa_id[]">
                                        {{ $item['depreciation_coa_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['value'] }}" name="value[]">
                                        {{ formatNumber($item['value']) }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['residual_value'] }}" name="residual_value[]">
                                        {{ formatNumber($item['residual_value']) }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['depreciation_percentage'] }}" name="depreciation_percentage[]">
                                        {{ $item['depreciation_percentage'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['estimated_life'] }}" name="estimated_life[]">
                                        {{ $item['estimated_life'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['depreciation_value'] }}" name="depreciation_value[]">
                                        {{ formatNumber($item['depreciation_value']) }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['depreciation_end_date'] }}" name="depreciation_end_date[]">
                                        {{ $item['depreciation_end_date'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['division_id'] }}" name="division_id[]">
                                        {{ $item['division_data']->name ?? '' }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['initial_location'] }}" name="initial_location[]">
                                        {{ $item['initial_location'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['note'] }}" name="note[]">
                                        {{ $item['note'] }}
                                    </td>
                                    <td>
                                        <input type="hidden" value="{{ $item['status'] }}" name="status[]">
                                        {{ $item['status'] }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-10">
                    <x-button type="submit" color="primary" icon="save" label="Simpan" />
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
    </script>
@endsection
