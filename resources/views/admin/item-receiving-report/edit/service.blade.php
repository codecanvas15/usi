@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report-service';
    $title = 'berita acara serah terima';
@endphp

@section('title', Str::headline("Edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route('admin.item-receiving-report.index') }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="edit {{ $title }}">
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="branch_id" id="branch_id" label="branch" required aria-disabled="">
                                    <option value="{{ $model->branch?->id }}" selected>
                                        {{ $model->branch?->name }}</option>
                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="vendor_id" label="purchase order" value="{{ $model->reference->spk_number ? $model->reference->spk_number . ' / ' : '' }} {{ $model->reference->code }}" required id="vendor-form" disabled />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="vendor_id" label="vendor" value="{{ $model->reference->vendor->nama }}" required id="vendor-form" disabled />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="created_at" label="dateCreated" value="{{ toDayDateTimeString($model->reference->created_at) }}" required id="dateCreated-form" disabled />
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date_receive" id="dateReceive-form" label="tanggal diterima" value="{{ \Carbon\Carbon::parse($model->date_receive)->format('d-m-Y') }}" required onchange="checkClosingPeriod($(this))" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" name="file" label="file" id="" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                            </div>
                        </div>
                        <div class="col-md-2 align-items-center d-flex">
                            @if ($model->file)
                                <x-button color="info" link="{{ url('storage/' . $model->file) }}" size="sm" label="Show File" fontawesome target="_blank" />
                            @endif
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="item" id="details-card">
                <x-slot name="table_content">
                    <div id="details-item">

                    </div>
                </x-slot>
            </x-card-data-table>

            <x-card-data-table>
                <x-slot name="table_content">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button class="save-data" type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {

            let ITEM_RECEIVING_REPORT = [];
            checkClosingPeriod($('#dateReceive-form'));

            const init = () => {
                getData();
            };

            const getData = () => {
                $.ajax({
                    type: "GET",
                    url: "{{ route('admin.item-receiving-report-service.detail-for-edit-data', $model) }}",
                    success: function({
                        data
                    }) {

                        ITEM_RECEIVING_REPORT = data;
                        displayItems();
                    }
                });
            };

            const displayItems = () => {
                ITEM_RECEIVING_REPORT.map((data, index) => {
                    let html = `
                        <div class="row">
                            <input type="hidden" name="item_receiving_report_detail_id[]" value="${data.id}" />
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="item_id" name="item" label="item" value="${data.item.nama} - ${data.item.kode}" required readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" id="jumlah-data-${index}" name="jumlah" label="quantity" value="${formatRupiahWithDecimal(data.reference.quantity_received - data.jumlah_diterima)} / ${formatRupiahWithDecimal(data.reference.quantity)} - ${data.item.unit?.name}" required helpers="sudah diterima / dipesan" readonly />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <x-input type="text" class="commas-form" id="jumlah-diterima-${index}" class="commas-form" name='jumlah_diterima[]' value="${formatRupiahWithDecimal(data.jumlah_diterima)}" label="jumlah_diterima" helpers="${data.item.unit?.name}" required />
                                </div>
                            </div>
                        </div>
                    `;

                    $('#details-item').append(html);

                    $(`#jumlah-diterima-${index}`).on('keyup', function() {
                        let value = thousandToFloat($(this).val());
                        let available = data.reference.quantity - (data.reference.quantity_received - data.jumlah_diterima);

                        if (value > available) {
                            $(this).val(formatRupiahWithDecimal(available));

                            alert('Jumlah diterima tidak boleh melebihi jumlah yang belum diterima');
                        }
                    });

                    initCommasForm();
                });
            };

            init();
        });
    </script>

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#item-receiving-report');
    </script>
@endsection
