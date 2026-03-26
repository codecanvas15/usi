@extends('layouts.admin.layout.index')

@php
    $main = 'master-user-assessment';
    $title = 'Master User Assessment';
@endphp

@section('title', Str::headline("tambah $title") . ' - ')

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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline("tambah $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $main")
        <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
            @csrf
            <x-card-data-table title="tambah {{ $title }}">
                <x-slot name="header_content">
                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="text" name="name" value="" label="Nama" id="name" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input type="number" name="weight" value="" label="Bobot (persentase)" id="weight" required />
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-select name="type" label="Tipe" id="type" required>
                                    <option value="" selected>-- pilih tipe --</option>
                                    <option value="key behavioral competencies">Key Behavioral Competencies</option>
                                    <option value="key skill competencies">Key Skill Competencies</option>
                                </x-select>
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
        sidebarActive('#user-assessment-sidebar')
    </script>
@endsection
