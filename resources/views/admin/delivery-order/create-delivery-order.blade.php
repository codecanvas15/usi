@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
@endphp

@section('title', Str::headline("create $main") . ' - ')

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
                        <a href="{{ route("admin.$main.show", $so) }}">{{ Str::headline("Delivery order $main") }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('create ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'create ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route("admin.$main.list-delivery-order.store", $so) }}" method="post">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <x-select name="vehicle_id" id="vehicle" label="vehicle" value="{{ $so->vehicle ?? '' }}" required>

                        </x-select>
                    </div>
                    <div class="col-md-4">
                        <x-input type="text" id="kapasitas" name="kapasitas" required disabled />
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-4">
                        <x-select name="sh_number_id" id="sh_number_id" label="sh_number" value="{{ $so->sh_number_id ?? '' }}" required>

                        </x-select>
                    </div>
                </div>

                <div id="do-main-form">
                    <div class="row mt-30">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" id="alokasi" name="kuantitas_kirim[]" label="alokasi" required />
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h3>Supply</h3>
                    <div id="supply">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="kuantitas_muat" name="kuantitas_muat[]" label="kuantitas_muat" />
                                    <small class="text-primary unit-info"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="realisasi_muat" name="realisasi_muat[]" label="realisasi_muat" />
                                    <small class="text-primary unit-info"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input class="datepicker-input" id="tangal_muat" name="tangal_muat" label="tangal_muat" />
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <h3>Drop</h3>
                    <div id="supply">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="kuantitas_bongkar" name="kuantitas_bongkar[]" label="kuantitas_bongkar" />
                                    <small class="text-primary unit-info"></small>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="realisasi_bongkar" name="realisasi_bongkar[]" label="realisasi_bongkar" />
                                    <small class="text-primary unit-info"></small>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" id="tangal_bongkar" name="tangal_bongkar" label="tangal_bongkar" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row mt-30">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="segel_atas" name="segel_atas[]" label="segel_atas" value="{{ $model->segel_atas ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="segel_bawah" name="segel_bawah[]" label="segel_bawah" value="{{ $model->segel_bawah ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="temperature" name="temperature[]" label="temperature" value="{{ $model->temperature ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="meter_awal" name="meter_awal[]" label="meter_awal" value="{{ $model->meter_awal ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="meter_akhir" name="meter_akhir[]" label="meter_akhir" value="{{ $model->meter_akhir ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="sg_meter" name="sg_meter[]" label="sg_meter" value="{{ $model->sg_meter ?? '' }}" />
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="row mt-10">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" id="keterangan" name="keterangan[]" label="keterangan" value="{{ $model->keterangan ?? '' }}" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" id="file" name="file[]" label="file" value="{{ $model->file ?? '' }}" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </form>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {
            initSelect2Search(`vehicle`, "{{ route('admin.select.vehicle') }}", {
                id: "id",
                text: "nama"
            });

            initSelect2Search(`sh_number_id`, "{{ route('admin.select.sh-numbers-for-so', $so) }}/", {
                id: "id",
                text: "kode"
            });

            // event form
            $(`#vehicle`).change(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.vehicle-fleet.detail') }}/${this.value}`,
                    success: function({
                        data
                    }) {
                        $(`#kapasitas`).val(data.kapasitas);
                    }
                });
            });

        });
        sidebarMenuOpen('#trading');
        sidebarActive('#delivery-order')
    </script>
@endsection
