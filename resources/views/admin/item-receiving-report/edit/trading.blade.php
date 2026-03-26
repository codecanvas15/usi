@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report-trading';
    $title = 'Laporan Penerimaan barang trading';
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
    @can('edit item-receiving-report-trading')
        <form action="{{ route("admin.$main.update", $model) }}" method="post" id="form-lpb" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <x-card-data-table title="Edit {{ $title }}">
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
                                <x-input type="text" name="" id="" value="{{ $model->reference->nomor_po }}" label="purchase order" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="customer" id="customer_name" value="{{ $model->reference->customer->nama }}" label="customer" required readonly />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="tanggal_dibuat" id="tanggal_dibuat" value="{{ toDayDateTimeString($model->reference->created_at) }}" label="Purchase dibuat pada" required readonly />
                            </div>
                        </div>
                        @php
                            $data_sh_number = $model->reference->sale_order ? $model->reference->sale_order->sh_number : $model->reference->sh_number;
                        @endphp
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">SH No.</label>
                                <p id="sh_number">{{ $data_sh_number->kode ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Supply Point</label>
                                <p id="supply_point">{{ $data_sh_number->sh_number_details[0]->alamat ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Drop Point</label>
                                <p id="drop_point">{{ $data_sh_number->sh_number_details[1]->alamat ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="col-md-12"></div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="loading_order" id="loading_order" value="{{ $model->item_receiving_report_po_trading->loading_order }}" label="loading_order" required readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date_receive" id="date_receive" label="tanggal diterima" value="{{ \Carbon\Carbon::parse($model->date_receive)->format('d-m-Y') }}" required onchange="checkClosingPeriod($(this))" />
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

                    <div class="row border-bottom border-primary">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="sale_confirmation" id="sale-confirmation" label="SCO Supplier" value="{{ $model->reference->sale_confirmation }}" helpers="sale confirmation dari purchase order" required readonly />
                            </div>
                        </div>
                    </div>

                    <div class="row mt-10">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="ware_house_id" label="warehouse" id="warehouse-select" required>
                                    <option value="{{ $model->ware_house_id }}" selected>{{ $model->ware_house->nama }}</option>
                                </x-select>
                            </div>
                        </div>
                    </div>

                </x-slot>

            </x-card-data-table>

            <x-card-data-table title="Item">
                <x-slot name="table_content">
                    @php
                        $detail = $model->item_receiving_report_po_trading;
                        $item = $model->item_receiving_report_po_trading->item;
                        $po_detail = $model->reference->po_trading_detail;

                        $jumlah = $po_detail == 'Kilo Liter' ? $po_detail->jumlah * 1000 : $po_detail->jumlah;
                    @endphp
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="item" id="item" label="item" value="{{ $item->nama }} - {{ $item->kode }}" required readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="jumlah" id="jumlah" label="quantity" value="{{ formatNumber($jumlah) }}" required readonly />
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="jumlah_tersedia" class="commas-form" value=" {{ formatNumber($jumlah - $po_detail->jumlah_lpbs + $detail->liter_obs) }} / {{ formatNumber($jumlah) }}" id="" label="jumlah tersedia" readonly />
                                <small class="text-primary">{{ $po_detail->item->unit->name ?? '' }}</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="liter_15" class="commas-form" value="{{ formatNumber($detail->liter_15) }}" id="" label="liter 15" />
                                <small class="text-primary">{{ $po_detail->item->unit->name ?? '' }}</small>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-input type="text" name="liter_obs" class="commas-form" id="" value="{{ formatNumber($detail->liter_obs) }}" label="liter obs" required />
                                <small class="text-primary">{{ $po_detail->item->unit->name ?? '' }}</small>
                            </div>
                        </div>
                    </div>
                </x-slot>

            </x-card-data-table>

            <x-card-data-table title="Additional Item">
                <x-slot name="table_content">
                    @foreach ($model->item_receiving_po_trading_additionals as $additional)
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <input type="hidden" name="additional_item_id[]" value="{{ $additional->purchase_order_additional_items_id }}">
                                    <x-input type="text" name="additional_item[]" id="item" label="item" value="{{ $additional->purchase_order_additional_items->item->nama }} - {{ $additional->purchase_order_additional_items->item->kode }}" required readonly />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_jumlah[]" id="jumlah" label="quantity" value="{{ formatNumber($additional->purchase_order_additional_items->jumlah) }}" required readonly />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_jumlah_tersedia[]" class="commas-form" value=" {{ formatNumber($additional->outstanding_qty) }}" id="" label="jumlah tersedia" readonly />
                                    <small class="text-primary">{{ $additional->purchase_order_additional_items->item->unit->name ?? '' }}</small>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" name="additional_receive_qty[]" class="commas-form" id="" value="{{ formatNumber($additional->receive_qty) }}" label="jumlah diterima" required />
                                    <small class="text-primary">{{ $additional->purchase_order_additional_items->item->unit->name ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                    @endforeach
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
        checkClosingPeriod($('#date_receive'));
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#item-receiving-report');
    </script>
@endsection
