@extends('layouts.admin.layout.index')

@php
    $main = 'closing-period';
    $title = 'closing';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'Detail ' . $title }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Sampai Tanggal') }}</label>
                                    <p>{{ localDate($model->to_date) }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>{{ Str::headline($model->status) }}</p>
                                </div>
                            </div>
                        </div>

                        @foreach ($model->closingPeriodCurrencies as $closingPeriodCurrency)
                            <div class="row mt-10">
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Mata Uang</label>
                                        <p>{{ $closingPeriodCurrency->currency->nama }} - {{ $closingPeriodCurrency->currency->negara }}</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        <label for="">Nilai Tukar</label>
                                        <p>{{ formatNumber($closingPeriodCurrency->exchange_rate) }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </x-slot>

                    <x-slot name="footer">

                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
            </div>
        </div>
        <x-card-data-table title="{{ 'Data Log' }}">
            <x-slot name="table_content">
                <ul class="list-group">
                    @forelse ($activity_logs as $item)
                        <li class="list-group-item">
                            <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                            <p class="mb-0">{{ Str::title($item->description) }}</p>
                            <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                {{ toDayDateTimeString($item->created_at) }}</small>
                        </li>
                    @empty
                        <li class="list-group-item">
                            <h5 class="fw-bold">Empty</h5>
                        </li>
                    @endforelse
                </ul>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#closing-period');
    </script>
@endsection
