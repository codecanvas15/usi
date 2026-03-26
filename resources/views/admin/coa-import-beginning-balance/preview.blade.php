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
    <form action="{{ route('admin.coa.coa-beginning.store') }}" method="post" enctype="multipart/form-data">
        @csrf
        <x-card-data-table :title="$main">
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-3">
                        <x-input name="date" label="date" value="" required class="datepicker-input" />
                    </div>
                </div>
                @foreach ($results as $result)
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="coa_information[]" label="coa_information" value="{{ $result['account_code'] . ' - ' . $result['account_name'] }}" required readonly />
                                <input type="hidden" name="coa_id[]" value="{{ $result['coa_id'] }}">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="credit[]" label="credit" value="{{ formatNumber($result['credit']) }}" required class="commas-form" />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input name="debit[]" label="debit" value="{{ formatNumber($result['debit']) }}" required class="commas-form" />
                            </div>
                        </div>
                    </div>
                @endforeach
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-3">
                    <x-button color="primary" label="Save" icon="save" fontawesome id="btn-submit" />
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-coa-sidebar');
        sidebarActive('#coa-sidebar');
    </script>
@endsection
