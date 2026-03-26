@extends('layouts.admin.layout.index')

@php
    $main = 'purchase-request-trading';
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
    <form action="{{ route("admin.$main.store") }}" method="post" id="form_purchase_request" enctype="multipart/form-data">
        <x-card-data-table title="{{ 'tambah ' . $main }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                @csrf

                <div class="row mt-20">
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="branch_id" id="branch_id" label="branch" required>
                                <option value="{{ get_current_branch()->id }}">{{ get_current_branch()->name }}</option>
                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="tanggal" name="date" value="{{ \Carbon\Carbon::today()->format('d-m-Y') }}" onchange="checkClosingPeriod($(this))" autofucus required />
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="customer_id" id="customer_id" label="customer" required>

                            </x-select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-select name="sh_number_id" id="sh_number_id" label="sh no." required>

                            </x-select>
                        </div>
                    </div>
                </div>
                <hr class="my-20">
                <div class="mb-10">
                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="item_id" id="item_id" label="item" required></x-select>
                        </div>
                        <div class="col-md-3">
                            <x-input name="qty" id="qty" label="quantity" required class="commas-form text-end" />
                        </div>
                    </div>
                    <div class="row mt-30">
                        <div class="col-md-6">
                            <x-text-area name="note" label="keterangan" id="note" cols="30" rows="10" required></x-text-area>
                        </div>
                    </div>
                </div>
                <div class="box-footer" id="position_btn_submit">
                    <div class="float-end">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>

    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#purchase-menu');
        sidebarActive('#purchase-request');

        initSelect2SearchPaginationData('customer_id', `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        });

        initSelect2SearchPaginationData('item_id', `{{ route('admin.select.item.type') }}/trading`, {
            id: "id",
            text: "nama"
        });

        $('#customer_id').change(function() {
            var id = $(this).val();
            $('#sh_number_id').empty();
            initSelect2SearchPaginationData('sh_number_id', `{{ route('admin.select.sh-number.customer') }}/${id}`, {
                id: "id",
                text: "kode,supply_point,drop_point"
            });
        })
    </script>
    @if (get_current_branch()->is_primary)
        <script>
            initSelect2SearchPaginationData('branch_id', `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name",
            })
        </script>
    @endif
@endsection
