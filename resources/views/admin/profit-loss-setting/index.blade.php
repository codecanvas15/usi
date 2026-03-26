@extends('layouts.admin.layout.index')

@php
    $main = 'profit-loss-setting';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
    <style>
        tr.selected {
            background-color: #959595 !important;
        }
    </style>
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
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ $main }}">
        <x-slot name="header_content">
            <div class="text-end mb-20">
                <x-button color="info" label="refresh" class="btn-sm" type="button" fontawesome icon="refresh" onclick="refresh()" />
            </div>
        </x-slot>
        <x-slot name="table_content">
            <div id="table-data"></div>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js" integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/admin/profit-loss-setting/index.js') }}?v=100"></script>
    <script>
        $(document).ready(function() {
            get_data();
            // setTimeout(() => {
            //     init_sortable();
            // }, 2000);
        });
    </script>
@endsection
