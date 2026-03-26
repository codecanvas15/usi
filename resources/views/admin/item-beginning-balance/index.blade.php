@extends('layouts.admin.layout.index')

@php
    $main = 'Import Beginning Balance Item';
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
                        <a href="{{ route('admin.item.index') }}">Coa</a>
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
    <form action="{{ route('admin.item.beginning-balance.import') }}" method="post" enctype="multipart/form-data">
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
                            <x-button type="button" color="info" icon="download" label="import format" dataToggle="modal" dataTarget="#import-format-modal" />
                        </div>
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>

    <form action="{{ route('admin.item.beginning-balance.import-format') }}" method="post">
        @csrf
        <x-modal title="Import format" id="import-format-modal" headerColor="primary">
            <x-slot name="modal_body">
                <div class="form-group">
                    <x-select name="ware_house_id" label="gudang" id="warehouse-select" required="required">

                    </x-select>
                </div>
            </x-slot>
            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                <x-button type="submit" color="primary" label="Download Excel" />
            </x-slot>
        </x-modal>
    </form>
@endsection

@section('js')
    <script>
        $(document).ready(function() {
            function initWarehouseSelect(element) {
                var select2Option = {
                    placeholder: "Pilih Data",
                    minimumInputLength: 3,
                    dropdownParent: $('#import-format-modal'),
                    allowClear: true,
                    width: "100%",
                    language: {
                        inputTooShort: () => {
                            return "Ketik minimal 3 karakter";
                        },
                        noResults: () => {
                            return "Data tidak ditemukan";
                        },
                    },
                    ajax: {
                        url: `${base_url}/select/ware-house`,
                        dataType: "json",
                        delay: 250,
                        type: "get",
                        data: ({
                            term
                        }) => {
                            let result = {};
                            result["search"] = term;
                            return result;
                        },
                        processResults: ({
                            data
                        }) => {
                            let final_data = data.map((data, key) => {
                                return {
                                    id: data.id,
                                    text: `${data.nama}`,
                                };
                            });
                            return {
                                results: final_data,
                            };
                        },
                        cache: true,
                    },
                };

                let elements = $(element);
                if (elements.length > 1) {
                    $.each(elements, function(e) {
                        $(this).select2(select2Option);
                    });
                } else {
                    $(element).select2(select2Option);
                }
            }

            initWarehouseSelect('#warehouse-select');

        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item')
    </script>
@endsection
