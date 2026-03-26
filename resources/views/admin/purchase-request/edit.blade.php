@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request';
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
    @canany(['edit purchase-request-service', 'edit purchase-request-general', 'edit purchase-request-transport'])
        <form action="{{ route("admin.$main.update", $model) }}" method="post" enctype="multipart/form-data">
            @if ($model->type == 'general')
                @include('admin.purchase-request.edit.general')
            @endif

            @if ($model->type == 'jasa')
                @include('admin.purchase-request.edit.service')
            @endif
        </form>
    @endcanany
@endsection

@section('js')
    @canany(['edit purchase-request-service', 'edit purchase-request-general', 'edit purchase-request-transport'])
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/ItemForPurchaseRequest.js') }}"></script>
        <script src="{{ asset('js/admin/select/project.js') }}"></script>
    @endcanany

    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase-request');
        initSelect2SearchPaginationData(`project-select`, `{{ route('admin.select.project') }}`, {
            id: 'id',
            text: 'nama,code'
        });
    </script>
@endsection
