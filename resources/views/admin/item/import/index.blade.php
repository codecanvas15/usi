@extends('layouts.admin.layout.index')

@php
    $main = 'item';
@endphp

@section('title', Str::headline("Import $main") . ' - ')

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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Import ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'Import ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route('admin.item.import') }}" method="post" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="file" name="file" required="required" />
                            </div>
                        </div>
                        <div class="col-md-4 d-flex align-items-center">
                            <div class="form-group">
                                <x-button type="submit" color="info" icon="download" label="import" />
                                <x-button type="button" :link="route('admin.item.import-format')" target="_blank" color="info" icon="download" label="import format" />
                            </div>
                        </div>
                    </div>

                </form>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@push('script')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item');
    </script>
@endpush
