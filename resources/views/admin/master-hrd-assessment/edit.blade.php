@extends('layouts.admin.layout.index')

@php
    $main = 'master-hrd-assessment';
    $title = 'Master HRD Assessment';
@endphp

@section('title', Str::headline("edit $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline("edit $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("edit $main")
        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <x-card-data-table title="edit {{ $title }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-input type="text" name="title" value="{{ $model->title }}" label="Judul" id="" required />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <x-text-area name="description" label="Deskripsi" id="" required>{{ $model->description }}</x-text-area>
                            </div>
                        </div>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-2">
                        <x-button color="secondary" label="kembali" icon="x" fontawesome size="sm" />
                        <x-button color="primary" label="simpan" icon="save" fontawesome size="sm" />
                    </div>
                </x-slot>
            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            //
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-hrd-assessment-sidebar');
        sidebarActive('#hrd-assessment-sidebar')
    </script>
@endsection
