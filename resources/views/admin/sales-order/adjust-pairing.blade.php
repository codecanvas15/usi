@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Adjust Pairing ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'adjust pairing ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="" label="nomor so" value="{{ $model->nomor_so }} - {{ $model->customer->nama }}" required disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="" label="qty dipesan" value="{{ formatNumber($model->so_trading_detail->jumlah) }}" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="" label="qty dialokasikan" value="{{ formatNumber($model->so_trading_detail->sudah_dialokasikan) }}" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required disabled />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" name="" label="qty dikirim" value="{{ formatNumber($model->so_trading_detail->sudah_dikirim) }}" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required disabled />
                        </div>
                    </div>
                </div>

                <hr class="mt-30 mb-20">
                <form action="" method="post">
                    @csrf
                    @foreach ($model->so_trading_detail->pairing_so_to_pos as $pairing)
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="" name="nomor_po" value="{{ $pairing->po_trading_detail->po_trading->nomor_po }} - {{ $pairing->po_trading_detail->po_trading->customer->nama }}" label="" required readonly />
                                    <input type="hidden" name="pairing_id[]" value="{{ $pairing->id }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="" class="commas-form" name="alokasi" value="{{ formatNumber($pairing->alokasi) }}" label="" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" id="re_alokasi" class="commas-form" name="re_alokasi[]" value="" label="re alokasi" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required />
                                </div>
                            </div>

                            @if ($loop->index == 0)
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <x-input type="text" id="sisa" name="sisa" label="" helpers="{{ $model->so_trading_detail->item->unit->name ?? '' }}" required disabled />
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <div class="row">
                        <div class="col-md-3"></div>
                        <div class="col-md-3"></div>
                        <div class="col-md-3">
                            <x-button color='primary' fontawesome class="float-end" icon="sliders" label="adjust pairing" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        $(document).ready(function() {
            let alokasi_list = [];
            let re_alokasi_list = [];
            let total_alokasi = 0;
            let total_re_alokasi = 0;
            let dipesan = {{ $model->so_trading_detail->jumlah }};

            $('input[name="alokasi"]').each(function() {
                alokasi_list.push(thousandToFloat($(this).val()));
            });

            total_alokasi = alokasi_list.reduce((a, b) => {
                return a + b;
            });


            $('input[id="re_alokasi"]').each(function() {
                $(this).keyup(function(e) {
                    let re_alokasi = thousandToFloat($(this).val());
                    let index = $(this).parent().parent().parent().index();

                    re_alokasi_list[index] = re_alokasi;

                    total_alokasi = 0;
                    total_re_alokasi = 0;

                    re_alokasi_list.forEach(function(item) {
                        total_re_alokasi += item;
                    });

                    let sisa = dipesan - total_re_alokasi;

                    $('#sisa').val(numberWithCommas(sisa));
                });
            });



            $('#sisa').val(dipesan - total_alokasi);
        });
    </script>
@endsection
