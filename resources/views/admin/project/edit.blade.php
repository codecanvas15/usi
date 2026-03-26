@extends('layouts.admin.layout.index')

@php
    $main = 'project';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

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
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <div class="row">
            <div class="col-md-6">
                <x-card-data-table title="{{ 'edit ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('put')

                            <div class="row">
                                <div class="col-md-12">
                                    <x-select name="branch_id" id="branch_id" label="branch">
                                        @if ($model->branch)
                                            <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-input class="datepicker-input" name="date" label="tanggal" value="{{ localDate($model->date) }}" id="" />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-input type="text" name="name" label="nama" id="" value="{{ $model->name }}" required autofocus />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-input type="text" name="location" value="{{ $model->location }}" label="location" id="" required />
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-input type="file" name="file" label="SPK" label="" id="" />
                                    </div>
                                </div>
                                {{-- <div class="col-12">
                                    <div class="form-group">
                                        <x-input type="text" name="budget" value="{{ formatNumber($model->budget) }}" class="commas-form" label="budget" id="" />
                                    </div>
                                </div> --}}
                                <div class="col-12">
                                    <div class="form-group">
                                        <x-text-area name="description" label="deskripsi" id="" cols="30" rows="10">
                                            {{ $model->description }}
                                        </x-text-area>
                                    </div>
                                </div>
                            </div>

                            <div class="box-footer">
                                <div class="d-flex justify-content-end gap-3">
                                    <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                    <x-button type="submit" color="primary" label="Save data" />
                                </div>
                            </div>

                        </form>
                    </x-slot>

                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#project-sidebar');
    </script>
@endsection
