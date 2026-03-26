@extends('layouts.admin.layout.index')

@php
    $main = 'Coa warning';
    $title = 'Coa warning';
    $route_name = 'coa-warning';
@endphp

@section('title', Str::headline($main) . ' - ')

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

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <x-select name="type" label="type" id="type-selectForm" required>
                            <option value="">-------------------------</option>
                            @foreach ($types as $key => $type)
                                <option value="{{ $key }}">
                                    {{ $type }}
                                </option>
                            @endforeach
                        </x-select>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <div class="form-group">
                        <x-button color='info' icon='search' fontawesome size="sm" id="btn-search" />
                    </div>
                </div>
            </div>

            <div class="mt-20 row" id="results">

            </div>

        </x-slot>
    </x-card-data-table>
@endsection

@section('js')
    <script>
        $(document).ready(function() {

            const displayResults = (results) => {
                $('#results').html('');
                const init = () => {
                    if ($('#type-selectForm').val() == 'tax') {
                        displayTax();
                    } else {
                        displayOthers();
                    }
                };

                const displayTax = () => {
                    results.map((result, index) => {
                        let {
                            coa_purchase_data_coa_code,
                            coa_purchase_data_coa_id,
                            coa_purchase_data_coa_name,
                            coa_sale_data_coa_code,
                            coa_sale_data_coa_id,
                            coa_sale_data_coa_name,

                            parent_code,
                            parent_id,
                            parent_name,
                            route
                        } = result;

                        let html = `
                            <div class="col-md-3 mb-4">
                                <div class="border p-10 shadow rounded-2">
                                    <a href="${route}" target="_blank" rel="noopener noreferrer" style="white-space: nowrap; overflow: hidden; display: block; text-overflow: ellipsis">
                                        <h5 class="fw-bolder">${parent_code} - ${parent_name}</h5>
                                        <p>Purchase ${coa_purchase_data_coa_code} - ${coa_purchase_data_coa_name}</p>
                                        <p>Sale ${coa_sale_data_coa_code} - ${coa_sale_data_coa_name}</p>
                                    </a>
                                </div>
                            </div>
                        `;

                        $('#results').append(html);
                    });
                };

                const displayOthers = () => {
                    results.map((result, index) => {
                        let {
                            coa_code,
                            coa_id,
                            coa_name,
                            parent_code,
                            parent_id,
                            parent_name,
                            route
                        } = result;

                        let html = `
                            <div class="col-md-3 mb-4">
                                <div class="border p-10 shadow rounded-2">
                                    <a href="${route}" target="_blank" rel="noopener noreferrer" style="white-space: nowrap; overflow: hidden; display: block; text-overflow: ellipsis">
                                        <h5 class="fw-bolder">${parent_code} - ${parent_name}</h5>
                                        <p>${coa_code} - ${coa_name}</p>
                                    </a>
                                </div>
                            </div>
                        `;

                        $('#results').append(html);
                    });
                };

                init();
            };

            $('#btn-search').click(function(e) {
                e.preventDefault();

                if ($('#type-selectForm').val()) {
                    $('#results').html('<h1 class="text-center">Loading...</h1>');
                    $.ajax({
                        type: "POST",
                        url: "{{ route('admin.coa-warning.get-list-of-data') }}",
                        data: {
                            type: $('#type-selectForm').val(),
                            _token: token,
                        },
                        success: function({
                            data
                        }) {
                            displayResults(data);
                        },
                        error: function({
                            message
                        }) {
                            alert(message ?? "Invalid request");
                        }
                    });
                } else {
                    $('#results').html('');
                }
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-coa-sidebar');
        sidebarActive('#warning-coa-sidebar');
    </script>
@endsection
