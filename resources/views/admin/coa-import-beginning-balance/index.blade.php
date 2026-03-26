@extends('layouts.admin.layout.index')

@php
    $main = 'Import Beginning Balance Coa';
@endphp

@section('title', Str::headline("$main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.coa.index') }}">Coa</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("$main") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form action="{{ route('admin.coa.coa-beginning.import') }}" method="post" enctype="multipart/form-data">
        @csrf
        <x-card-data-table :title="$main">
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="file" name="file" required="required" />
                        </div>
                    </div>
                    <div class="col-md-4 d-flex align-items-center">
                        <div class="form-group">
                            <x-button type="submit" color="info" icon="download" label="import" />
                            <x-button type="submit" color="info" link="{{ route('admin.coa.coa-beginning.import-format') }}" icon="download" label="import format" />
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-coa-sidebar');
        sidebarActive('#coa-sidebar');
    </script>
@endsection
