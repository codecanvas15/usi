@extends('layouts.admin.layout.index')

@php
    $main = 'price';
    $title = 'harga';
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
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                @can("create $main")
                    <div class="col-md-3 col-md-6 col-xl-4">
                        <x-button color="info" icon="plus" label="Create" link='{{ route("admin.$main.create") }}' />
                    </div>
                @endcan
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <div class="row mb-15">
                <div class="col-md-3">
                    <x-select label="tahun" id="tahun">
                        <option value="">Pilih data</option>
                        @foreach (range(Date('Y'), Date('Y', strtotime('+10 years'))) as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="col-md-3">
                    <x-select label="period" id="period_id" name="period_id" required>

                    </x-select>
                </div>
                <div class="col-md-3">
                    <x-select label="item" id="item_id" name="item_id">
                    </x-select>
                </div>
                <div class="col-md-1 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="setPriceTable" icon="search" fontawesome />
                    </div>
                </div>
            </div>
            <x-table id="dataTable">
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('Periode') }}</th>
                    <th>{{ Str::headline('Harga Beli') }}</th>
                    <th>{{ Str::headline('Harga Jual') }}</th>
                    <th>{{ Str::headline('Item') }}</th>
                    <th></th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(() => {
            const initTable = () => {
                const table = $('#dataTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    destroy: true,
                    ajax: {
                        url: '{{ route("admin.$main.index") }}',
                        data: {
                            tahun: $('#tahun').val(),
                            period_id: $('#period_id').val(),
                            item_id: $('#item_id').val()
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'periode',
                            name: 'period_id'
                        },
                        {
                            data: 'harga_beli',
                            name: 'harga_beli'
                        },
                        {
                            data: 'harga_jual',
                            name: 'harga_jual'
                        },
                        {
                            data: 'item.nama',
                            name: 'item.nama'
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
            }
            $('table').css('width', '100%')

            $('#tahun').on('change', function() {
                if ($(this).val().length == 4) {
                    $('#period_id').val('')
                    initSelect2Search(`period_id`, "{{ route('admin.select.period') }}/" + $(this).val(), {
                        id: "id",
                        text: "value"
                    });
                }
            })

            $('#setPriceTable').click(function() {
                initTable()
            })

            initTable()
            initSelect2Search(`item_id`, "{{ route('admin.select.item-trading') }}", {
                id: "id",
                text: "nama"
            });
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#price')
    </script>
@endsection
