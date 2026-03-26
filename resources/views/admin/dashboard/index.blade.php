@extends('layouts.admin.layout.index')

@php
    $main = 'Dashboard';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <div class="d-flex justify-content-between">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                </ol>
                <a href="#" class="link-rotate" id="reload-data">
                    <i class="fa-solid fa-rotate animate-rotate"></i>
                    Reload Data
                </a>
            </div>
        </div>
    </div>
@endsection

@section('css')
    <style>
        .scale {
            cursor: pointer;
        }

        .scale:hover {
            transform: scale(1.1);
            transition: .3s ease-in-out;
            z-index: 99;
            border: 1px solid var(--bs-primary);
        }

        .link-rotate:hover i.animate-rotate,
        .link-rotate:active i.animate-rotate,
        .link-rotate:focus i.animate-rotate {
            rotate: 180deg;
            transition: .3s ease-in-out;
        }
    </style>
@endsection

@section('content')
    <div class="row" id="authorization-alert"></div>
    @can('sales dashboard')
        @include('admin.dashboard.sales_new')
    @endcan

    @can('dashboard')
        {{-- @include('admin.dashboard.admin') --}}
    @endcan

    @can('purchase dashboard')
        {{-- @include('admin.dashboard.purchase') --}}
    @endcan

    @can('hrd dashboard')
        @include('admin.dashboard.hrd_new')
        {{-- @include('admin.dashboard.hrd') --}}
    @endcan

    @can('warehouse dashboard')
        {{-- @include('admin.dashboard.warehouse') --}}
    @endcan

    @can('accounting dashboard')
        {{-- @include('admin.dashboard.accounting') --}}
    @endcan

    @can('finance dashboard')
        @include('admin.dashboard.finance_new')
        {{-- @include('admin.dashboard.finance') --}}
    @endcan

    @can('invoice dashboard')
        {{-- @include('admin.dashboard.invoice') --}}
    @endcan

@endsection

@section('js')
    <script src="{{ asset('assets/vendor_components/apexcharts-bundle/dist/apexcharts.js') }}"></script>
    <script src="{{ asset('assets/vendor_components/progressbar.js-master/dist/progressbar.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarActive('#dashboard');
    </script>
@endsection
