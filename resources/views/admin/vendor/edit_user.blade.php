@extends('layouts.admin.layout.index')

@php
    $main = 'vendor';
@endphp

@section('title', Str::headline("Edir $main User") . ' - ')

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
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main . 'User') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'edit vendor User' }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.vendor.users.update-user', ['vendor_id', $vendor, 'user_id' => $user]) }}" method="post" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" label="name" value="{{ $user->name }}" name="name" required />
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="email" label="email" value="{{ $user->email }}" name="email" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="password" label="password" name="password" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="password" label="confirm_password" name="confirm_password" />
                        </div>
                    </div>
                </div>

                <div class="float-end">
                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                    <x-button type="submit" color="primary" label="Save data" />
                </div>
            </form>
        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-vendor-sidebar');
        sidebarActive('#vendor-sidebar')
    </script>
@endsection
