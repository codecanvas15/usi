@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $menu = 'pengajuan dana';
@endphp

@section('title', Str::headline($menu) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
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
        <x-slot name="header_content">

            @can("create $main")
                <div class="mb-4">
                    <x-button color="info" icon="plus" fontawesome dataToggle="modal" label="Create" dataTarget="#item-modal" />
                    <x-modal title="jenis pengajuan" id="item-modal" headerColor="success">
                        <x-slot name="modal_body">
                            <x-button color="info" label="general" link='{{ route("admin.$main.create") }}?item=general' />
                            <x-button color="success" label="lpb" link='{{ route("admin.$main.create") }}?item=lpb' />
                            <x-button color="primary" label="uang muka" link='{{ route("admin.$main.create") }}?item=dp' />
                        </x-slot>
                    </x-modal>
                </div>
            @endcan

        </x-slot>
        <x-slot name="table_content">

            <ul class="nav nav-tabs customtab2 mb-10" role="tablist">
                <li class="nav-item">
                    <a class="nav-link rounded active" data-bs-toggle="tab" href="#NonGiro-tab" id="NonGiro-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">Semua</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link rounded" data-bs-toggle="tab" href="#Giro-tab" id="Giro-btn" role="tab">
                        <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                        <span class="hidden-xs-down">Dengan Giro</span>
                    </a>
                </li>
            </ul>

            <div class="tab-content mt-30">
                <div class="tab-pane active" id="NonGiro-tab" role="tabpanel">
                    @include('admin.fund-submission.partial.index.NonGiro.NonGiroFIlter')
                    @include('admin.fund-submission.partial.index.NonGiro.NonGiroTable')
                </div>
                <div class="tab-pane" id="Giro-tab" role="tabpanel">
                    @include('admin.fund-submission.partial.index.Giro.GiroFIlter')
                    @include('admin.fund-submission.partial.index.Giro.GiroTable')
                </div>
            </div>

        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>

    @include('admin.fund-submission.partial.index.NonGiro.NonGiroScript');
    @include('admin.fund-submission.partial.index.Giro.GiroScript');

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#fund-submission');

        const download_recap = (format) => {
            const from_date = $("#from_date").val();
            const to_date = $("#to_date").val();
            const branch_id = $("#branch-select").val();
            const is_used = $("#is_used").val();
            const url = `{{ route('admin.fund-submission.download-recap') }}?from_date=${from_date}&to_date=${to_date}&branch_id=${branch_id}&is_used=${is_used}&format=${format}`;
            window.open(url, '_blank');
        }
    </script>
@endsection
