@extends('layouts.admin.layout.index')

@php
    $main = 'company';
    $title = 'Company Profile';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table :title="$title">
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Nama</label>
                            <p>{{ $company->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Singkatan</label>
                            <p>{{ $company->short_name }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Alamat</label>
                            <p>{{ $company->address }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Phone</label>
                            <p>{{ $company->phone }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Fax</label>
                            <p>{{ $company->fax }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Email</label>
                            <p>{{ $company->email }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Website</label>
                            <p>{{ $company->website }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>NPWP</label>
                            <p>{{ $company->npwp ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Logo</label>
                            <p>
                                <img src="{{ url('/storage/' . $company->logo) }}" alt="">
                            </p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>Secondary Logo</label>
                            <p>
                                <img src="{{ url('/storage/' . $company->secondary_logo) }}" alt="">
                            </p>
                        </div>
                    </div>
                </div>
            @endcan
        </x-slot>
        <x-slot name="footer">
            <x-button color='warning' fontawesome icon="edit" id="edit-btn" class="w-auto" size="sm" />
        </x-slot>

    </x-card-data-table>

    <form action="{{ route("admin.$main.update", $company) }}" method="post" enctype="multipart/form-data">
        @method('PUT')
        @csrf
        <x-card-data-table title="Edit Company Profile" id="edit-card">
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="name" label="Nama" value="{{ $company->name }}" required></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="short_name" label="singkatan" value="{{ $company->short_name }}" required></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="address" label="Alamat" value="{{ $company->address }}" required></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="phone" label="Phone" value="{{ $company->phone }}" required></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="fax" label="Fax" value="{{ $company->fax }}" required></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="email" label="email" value="{{ $company->email }}"></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="website" label="website" value="{{ $company->website }}"></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="logo" label="Logo" type="file"></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input name="secondary_logo" label="Secondary Logo" type="file"></x-input>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <x-input type="text" id="npwp" class="npwp-form-input" name="npwp" label="npwp" value="{{ $company->npwp ?? '' }}" />
                        </div>
                    </div>
                </div>
            </x-slot>
            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    @can("edit $main")
                        <x-button type="submit" color='primary' fontawesome icon="edit" label="Submit" class="w-auto" size="sm" />
                    @endcan
                </div>
            </x-slot>
        </x-card-data-table>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        sidebarMenuOpen('#setting');
    </script>
    <script>
        $(document).ready(function() {
            initNpwpInputForm()

            $('#edit-card').hide();

            $('#edit-btn').click(function(e) {
                e.preventDefault();
                $('#edit-card').show();
            });
        });
    </script>
@endsection
