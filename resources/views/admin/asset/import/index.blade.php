@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $menu = 'import aktiva tetap';
@endphp

@section('title', Str::headline($menu) . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('master') }}
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('aktiva tetap') }}
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
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.asset.process-import') }}" method="post" enctype="multipart/form-data">
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
                            <x-button type="button" color="info" icon="download" label="import format" link="{{ route('admin.asset.import-forma') }}" />
                        </div>
                    </div>
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
    </script>
@endsection
