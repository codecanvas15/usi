@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request';
@endphp

@section('title', Str::headline("Tambah $main") . ' - ')

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
                        {{ Str::headline('Tambah ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @canany(['create purchase-request-service', 'create purchase-request-general'])
        <form action="{{ route("admin.$main.store") }}" method="post" id="form_purchase_request" enctype="multipart/form-data">

            @if (request()->get('type') == 'general')
                @include('admin.purchase-request.create.general')
            @endif

            @if (request()->get('type') == 'service')
                @include('admin.purchase-request.create.service')
            @endif
        </form>
    @endcanany
@endsection

@section('js')
    @canany(['create purchase-request-service', 'create purchase-request-general'])
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/ItemForPurchaseRequest.js') }}"></script>
        <script src="{{ asset('js/admin/select/project.js') }}"></script>
        <script>
            sidebarMenuOpen('#purchase-menu');
            sidebarActive('#purchase-request');
            initProjectSelect('#project-select')
            initSelect2SearchPaginationData(`project-select`, `{{ route('admin.select.project') }}`, {
                id: 'id',
                text: 'name,code'
            })

            function handleSubmit() {
                $('#form_purchase_request').submit(function(e) {
                    if ($('#jumlah').val() == 0) {
                        e.preventDefault()
                        $('#position_btn_submit').html('')
                        $('#jumlah').val('')
                        showAlert('', 'Jumlah tidak boleh 0')
                        $('#position_btn_submit').html(`
                        <div class="float-end">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                        `)
                    } else if ($('#jumlah').val() == "NaN") {
                        e.preventDefault()
                        $('#position_btn_submit').html('')
                        $('#jumlah').val('')
                        showAlert('', 'Jumlah tidak boleh string')
                        $('#position_btn_submit').html(`
                        <div class="float-end">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                        `)
                    }
                })
            }

            $(document).ready(function() {
                handleSubmit()
            })
        </script>
    @endcanany
@endsection
