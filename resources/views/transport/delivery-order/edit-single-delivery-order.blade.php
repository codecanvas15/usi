@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
@endphp

@section('title', Str::headline("edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("transport.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("transport.$main.show", $model) }}">{{ Str::headline('detail purchase transport') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("transport.$main.show", $model) }}">{{ Str::headline('detail purchase transport') }}</a>
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
    <x-card-data-table title="{{ 'edit ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('transport.delivery-order.detail.update', ['purchase_transport' => $model, 'id' => $data]) }}" method="post" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- target delivery --}}
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input class="datepicker-input" name="target_delivery" label='target delivery' value="{{ localDate($data->target_delivery) }}" id="" readonly />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="nama driver" name="driver_name" value="{{ $model->driver_name }}" id="" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="nomor hp driver" name="driver_phone" value="{{ $model->driver_phone }}" id="" required />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" label="informasi kendaraan" name="vehicle_information" value="{{ $model->vehicle_information }}" id="" required />
                        </div>
                    </div>
                </div>
                {{-- target delivery --}}

                {{-- load and unload data --}}
                <div class="mt-20 pt-20 border-top border-bottom border-primary">
                    <div class="row">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" name="load_date" label="tanggal_muat" id="" value="{{ localDate($data->load_date ?? '') }}" />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="load_quantity" label="kapasitas muat" id="" value="{{ formatNumber($data->load_quantity ?? 0) }}" class="commas-form" helpers="{{ $data->so_trading->so_trading_detail->item->unit->name ?? '' }}" readonly />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="load_quantity_realization" label="kapasitas muat realisasi" id="" value="{{ formatNumber($data->load_quantity_realization) }}" helpers="{{ $data->so_trading->so_trading_detail->item->unit->name ?? '' }}" class="commas-form" />
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="row">
                        <div class="row">

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="date" name="unload_date" label="tanggal_bongkar" id="" value="{{ localDate($data->unload_date ?? '') }}" />
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" name="unload_quantity_realization" label="kapasitas bongkar realisasi" id="" value="{{ formatNumber($data->unload_quantity_realization) }}" helpers="{{ $data->so_trading->so_trading_detail->item->unit->name ?? '' }}" class="commas-form" />
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
                {{-- end load and unload data --}}

                {{-- details --}}
                <div class="row mt-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="top_seal" value="{{ $data->top_seal }}" label="segel atas" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="bottom_seal" value="{{ $data->bottom_seal }}" label="segel atas" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="temperature" value="{{ $data->temperature }}" label="temperatur" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="initial_meter" value="{{ $data->initial_meter }}" label="meter awal" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="initial_final" value="{{ $data->initial_final }}" label="meter akhir" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="sg_meter" value="{{ $data->sg_meter }}" label="sg_meter" id="" />
                        </div>
                    </div>
                </div>
                {{-- end details --}}

                {{-- file and description --}}
                <div class="row mt-20 border-top border-primary py-20">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="file" name="file" label="file" id="" />
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" name="description" label="deskripsi" value="{{ $data->description }}" id="" />
                        </div>
                    </div>
                </div>

                <div class="box-footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" required />
                        <x-button type="submit" color="primary" label="Save data" required />
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@push('script')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $(document).ready(function() {
            initCommasForm()
            sidebarActive('#transport-delivery-order')
        });
    </script>
@endpush
