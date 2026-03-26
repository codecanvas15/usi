@extends('layouts.admin.layout.index')

@php
    $main = 'model';
    $title = 'master otorisasi';
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
                <div class="col-md-3 col-md-6 col-xl-4">
                    <x-modal title="create new data" id="create-modal" headerColor="info">
                        <x-slot name="modal_body">
                            <form action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <x-input type="text" label="name" name="name" label="name" required />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="phone" name="phone" label="phone" required />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="address" name="address" label="address" required />
                                </div>
                                <div class="form-group">
                                    <x-input type="text" label="sort" name="sort" label="kode branch" required />
                                </div>
                                <div class="form-group">
                                    <x-input-checkbox label="Kantor Pusat" name="is_primary" id="is_primary" />
                                </div>
                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                <x-button type="submit" color="primary" label="Save data" />
                            </form>
                        </x-slot>
                    </x-modal>
                </div>
            </div>

            <div class="row" id="createdFilter">
                <div class="col-md-2">
                    <x-select name="kelompok" id="group" class="form-control">
                        <option value="">Pilih Kelompok</option>
                        @foreach ($groups as $group)
                            <option value="{{ $group }}">{{ Str::headline($group) }}</option>
                        @endforeach
                    </x-select>
                </div>
                <div class="col-md-1 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="group-submit" size="sm" icon="search" fontawesome onclick="$('table#model_table').DataTable().ajax.reload();"></x-button>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            @can("view $main")
                <x-table id="model_table">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('kelompok') }}</th>
                        <th>{{ Str::headline('menu') }}</th>
                        <th></th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            @endcan
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    @can("view $main")
        <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
        <script>
            $(document).ready(() => {
                const table = $('table#model_table').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: {
                        url: "{{ route('admin.model.index') }}",
                        data: function(data) {
                            data.group = $('#group').val();
                        }
                    },
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'group',
                            name: 'group'
                        },
                        {
                            data: 'alias',
                            name: 'alias'
                        },
                        {
                            data: 'is_complete',
                            name: 'is_complete',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'action',
                            name: 'action',
                            orderable: false,
                            searchable: false
                        },
                    ]
                });
                $('table').css('width', '100%');
            });
        </script>
    @endcan
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#model')
    </script>
@endsection
