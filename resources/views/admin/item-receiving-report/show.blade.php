@extends('layouts.admin.layout.index')

@php
    $main = 'item-receiving-report';
    $title = 'Laporan Penerimaan barang';
    if ($model->tipe == 'jasa' || $model->tipe == 'transport') {
        $title = 'berita acara serah terima';
    }
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

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
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <div>
        <div class="box text-white" style="background-image: linear-gradient(90deg, purple, rgb(74, 74, 74))">
            <div class="box-body">
                <div class="row justify-content-end">
                    <div class="col-md-6 align-self-center">
                        @php
                            $explodeCode = $model->tipe;
                            $title = '';
                            if (!empty($explodeCode)) {
                                switch ($explodeCode) {
                                    case 'trading':
                                        $title = 'LPB Trading';
                                        break;

                                    case 'general':
                                        $title = 'LPB General';
                                        break;

                                    case 'transport':
                                        $title = 'LPBTransport';
                                        break;

                                    default:
                                        $title = 'Berita Acara Serah Terima';
                                        break;
                                }
                            }
                        @endphp
                        <h4 class="m-0">Detail {{ $title }}</h4>
                        <h1 class="m-0">{{ $model->kode }}</h1>
                    </div>
                    <div class="col-md-6 align-self-center">
                        <div class="row justify-content-end">
                            <div class="col-md-3 d-flex flex-column">
                                <h5 class="text-center">{{ Str::headline('status_' . $title) }}</h5>
                                <div class="badge badge-lg badge-{{ purchase_request_status()[$model->status]['color'] }}">
                                    {{ Str::headline(purchase_request_status()[$model->status]['label']) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            @if ($model->tipe == 'general')
                @include('admin.item-receiving-report.details.general')
            @endif

            @if ($model->tipe == 'jasa')
                @include('admin.item-receiving-report.details.service')
            @endif

            @if ($model->tipe == 'trading')
                @include('admin.item-receiving-report.details.trading')
            @endif

            @if ($model->tipe == 'transport')
                @include('admin.item-receiving-report.details.transport')
            @endif

            @can('view journal')
                @include('components.journal-table')
            @endcan
        </div>

        <div class="col-md-4">
            {!! $authorization_log_view !!}

            <div id="print-request-container"></div>
            @php
                $type = $model?->tipe;

                if ($type == 'jasa') {
                    $type = 'item-receiving-report-service';
                } elseif ($type == 'general') {
                    $type = 'item-receiving-report-general';
                } elseif ($type == 'trading') {
                    $type = 'item-receiving-report-trading';
                } elseif ($type == 'transport') {
                    $type = 'item-receiving-report-transport';
                }
            @endphp

            <x-card-data-table>
                <x-slot name="table_content">
                    {{-- <x-button type="button" color="info" label="Export" target="_blank" icon="file" fontawesome soft block size="md" onclick="show_print_out_modal(event)" link="{{  }}" /> --}}
                    {{-- <x-button-auth-print type="" model="{{ \App\Models\ItemReceivingReport::class }}" did="{{ $model->id }}" href="{{ route($type . '.export-pdf', ['id' => encryptId($model->id)]) }}" link="{{  }}" code="{{ $model->kode }}" label="Export" /> --}}
                    @include('components.button-auth-print', [
                        'type' => 'lpb_' . ($model?->tipe == 'jasa' ? 'service' : $model->tipe),
                        'href' => route($type . '.export-pdf', ['id' => encryptId($model->id)]),
                        'model' => \App\Models\ItemReceivingReport::class,
                        'did' => $model->id,
                        'link' => route("admin.$type.show", $model),
                        'code' => $model->kode,
                    ])
                </x-slot>
            </x-card-data-table>

            <x-card-data-table title="{{ 'Status Log' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <ul class="list-group">
                        @foreach ($status_logs as $item)
                            <li class="list-group-item">
                                @if ($item->from_status && $item->to_status)
                                    <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                        {{ Str::headline($item->to_status) }}</h5>
                                @elseif (!$item->from_status && $item->to_status)
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                @endif
                                <p class="mb-0">{{ Str::title($item->message) }}</p>
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
            <x-card-data-table title="{{ 'Data Log' }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <ul class="list-group">
                        @foreach ($activity_logs as $item)
                            <li class="list-group-item">
                                <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                <p class="mb-0">{{ Str::title($item->description) }}</p>
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>

    @if ($model->tipe == 'trading')
        <x-modal title="approve item-receiving-report" id="approve-trading-modal" headerColor="success">
            <x-slot name="modal_body">
                <form action="" method="post" id="confirmation-form">
                    <div class="col">
                        <div class="form-group">
                            <label for="loadingOrderFormValidation">Loading Order <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-1" id="loadingOrderFormValidation" placeholder="Masukkan loading order...">
                            <p id="loadingOrderErrorFormValidation" class="mb-0 text-danger" style="display: none">Loading order tidak sama!</p>
                        </div>
                        <div class="form-group">
                            <label for="saleConfirmFormValidation">Sale Confirmation <span class="text-danger">*</span></label>
                            <input type="text" class="form-control mt-1" id="saleConfirmFormValidation" placeholder="Masukkan sale confirmation...">
                            <p id="saleConfirmErrorFormValidation" class="mb-0 text-danger" style="display: none">Sale confirmation tidak sama!</p>
                        </div>
                    </div>
                    <div class="mt-10 border-top pt-10">
                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                        <x-button id="btnApproveForm" type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                    </div>
                </form>
            </x-slot>
        </x-modal>
    @endif
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#item-receiving-report');

        @if ($model?->tipe == 'trading')
            let is_loading_order = false;
            let is_sale_confirm = false;

            let loading_order = @json($model?->item_receiving_report_po_trading?->loading_order);
            let sale_confirm = @json($model?->reference?->sale_confirmation);

            $('#loadingOrderFormValidation').keyup(debounce(function() {
                if ($(this).val() !== loading_order) {
                    $('#loadingOrderErrorFormValidation').css('display', 'block');
                    is_loading_order = false;
                } else {
                    is_loading_order = true;
                    $('#loadingOrderErrorFormValidation').css('display', 'none');
                }
            }, 500));

            $('#saleConfirmFormValidation').keyup(debounce(function() {
                if ($(this).val() !== sale_confirm) {
                    $('#saleConfirmErrorFormValidation').css('display', 'block');
                    is_sale_confirm = false;
                } else {
                    is_sale_confirm = true;
                    $('#saleConfirmErrorFormValidation').css('display', 'none');
                }
            }, 500));

            $('#confirmation-form').submit(function(e) {
                e.preventDefault();
                console.log(is_loading_order, is_sale_confirm);
                if (is_loading_order && is_sale_confirm) {
                    $('#approve-trading-modal').modal('hide');

                    $('#approve-form').append('<input type="hidden" name="loading_order" value="' + loading_order + '">');
                    $('#approve-form').append('<input type="hidden" name="sale_confirmation" value="' + sale_confirm + '">');
                    $('#approve-form').submit();
                } else {
                    alert('Loading order dan Sale confirmation harus sesuai!');
                    setTimeout(() => {
                        $(this).find('input[type=submit]').prop('disabled', false)
                        $(this).find('button[type=submit]').prop('disabled', false)
                    }, 1000);
                }
            });
        @endif
    </script>
    @if ($model->tipe == 'trading')
        <script>
            $('#approve-form').submit(function(e) {
                if (!is_loading_order || !is_sale_confirm) {
                    e.preventDefault();
                    $('#approve-trading-modal').modal('show');

                    setTimeout(() => {
                        $(this).find('input[type=submit]').prop('disabled', false)
                        $(this).find('button[type=submit]').prop('disabled', false)
                    }, 1000);
                }
            });
        </script>
    @endif
    <script>
        $('#history-button').on('click', function() {
            $.ajax({
                url: `{{ route('admin.' . $type . '.history', $model->id) }}`,
                success: function({
                    data
                }) {
                    $('#history-list').html('');
                    $.each(data, function(key, value) {
                        let link = `<a href="${value.link}" target="_blank" class="text-primary text-decoration-underline hover_text-dark">${value.code}</a>`;
                        $('#history-list').append(`
                                <tr>
                                    <td>${localDate(value.date)}</td>
                                    <td class="text-capitalize">${value.menu}</td>
                                    <td>${link}</td>
                                </tr>
                            `);
                    });

                    $('#history-modal').modal('show');
                }
            });
        });

        get_request_print_approval(`App\\Models\\ItemReceivingReport`, '{{ $model->id }}', 'lpb_{{ $model->tipe == 'jasa' ? 'service' : $model->tipe }}');
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\ItemReceivingReport`, '{{ $model->id }}');
        </script>
    @endcan

@endsection
