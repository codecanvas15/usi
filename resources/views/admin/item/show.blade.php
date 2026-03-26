@extends('layouts.admin.layout.index')

@php
    $main = 'item';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'detail ' . $main }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')
                <div class="row">
                    <div class="col-12 p-2 bg-danger rounded rounded-lg text-center mb-3">
                        <strong>{{ $model->kode }}</strong>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('nama') }}</label>
                            <p>
                                {{ $model->nama }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('deskripsi') }}</label>
                            <p>
                                {{ $model->deskripsi }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('jenis usaha') }}</label>
                            <p class="text-uppercase">
                                {{ $model->type }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('item type') }}</label>
                            <p class="text-uppercase">
                                {{ $model->item_category?->item_type?->nama }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('category') }}</label>
                            <div>
                                <a href="{{ route('admin.item-category.show', $model->item_category->id) }}" target="_blank" rel="noopener noreferrer">{{ $model->item_category?->nama }}</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('unit') }}</label>
                            <p>
                                {{ $model->unit?->name }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('status') }}</label>
                            <br>
                            <div class="badge badge-lg badge-{{ get_item_status()[$model->status]['color'] }}">
                                {{ get_item_status()[$model->status]['label'] }} -
                                {{ get_item_status()[$model->status]['text'] }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('file') }}</label>
                            <p>
                                <a href='{{ asset('storage/' . $model->file) }}' class="w-auto btn btn-sm btn-primary" download="{{ $model->nama }}">
                                    <i class="fa-solid fa-download"></i>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('created_at') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->created_at) }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">{{ Str::headline('last modified') }}</label>
                            <p>
                                {{ toDayDateTimeString($model->updated_at) }}
                            </p>
                        </div>
                    </div>
                </div>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan
                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-{{ $model->id }}' />

                        <x-modal-delete id="delete-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>
        @endcan

    </x-card-data-table>

    @can('view item-price')
        @if ($model->type != 'trading')
            <x-card-data-table title="item price">
                <x-slot name="header_content">
                    @can('create item-subtitute')
                        <div class="mb-4">
                            <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" />
                            <x-modal title="create new data" id="create-modal" headerColor="info">
                                <x-slot name="modal_body">
                                    <form action="{{ route('admin.item.price.store', $model->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        <input type="hidden" name="item_id" value="{{ $model->id }}">
                                        <div class="form-group">
                                            <x-input type="text" label="harga_jual" class="commas-form" name="harga_jual" required />
                                        </div>
                                        <div class="form-group">
                                            <x-input type="text" label="harga_beli" class="commas-form" name="harga_beli" required />
                                        </div>
                                        <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                        <x-button type="submit" color="primary" label="Save data" />
                                    </form>
                                </x-slot>
                            </x-modal>
                        </div>
                    @endcan
                </x-slot>
                <x-slot name="table_content">
                    <x-table id="item-price-datatable">
                        <x-slot name="table_head">
                            <th>{{ Str::headline('#') }}</th>
                            <th>{{ Str::headline('Harga Jual') }}</th>
                            <th>{{ Str::headline('Harga Beli') }}</th>
                            <th>{{ Str::headline('Created At') }}</th>
                            <th>{{ Str::headline('Last Modified At') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">

                        </x-slot>
                    </x-table>
                </x-slot>

            </x-card-data-table>
        @endif
    @endcan

    @can('view item-subtitute')
        <x-card-data-table title="item subtitute">
            <x-slot name="header_content">
                @can('create item-subtitute')
                    <div class="my-20">
                        <form action="{{ route('admin.item-subtitute.store') }}" method="post">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $model->id }}">
                            <div class="model">
                                <div class="col-md-3">
                                    <x-select name="child_id" id="child_id" label="Item" required>
                                    </x-select>
                                </div>
                                <div class="col-md-3 row align-items-end">
                                    <div class="form-group">
                                        <x-button type="submit" color="primary" class="w-auto" icon="save" fontawesome size="sm" />
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endcan
            </x-slot>
            <x-slot name="table_content">
                <x-table id="item-subtitute-datatable">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('Created At') }}</th>
                        <th>{{ Str::headline('Last Modified At') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">

                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@push('script')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    @can('create item-subtitutes')
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/admin/select/itemSelect.js') }}"></script>

        <script>
            inititemSelect('child_id')
        </script>
    @endcan

    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    @can('view item-price')
        <script>
            $(document).ready(function() {
                $(document).ready(() => {
                    const table = $('table#item-price-datatable').DataTable({
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        ajax: '{{ route('admin.item.price', $model->id) }}',
                        columns: [{
                                data: 'DT_RowIndex',
                                name: 'DT_RowIndex',
                                orderable: false,
                                searchable: false
                            },
                            {
                                data: 'harga_jual',
                                name: 'harga_jual'
                            },
                            {
                                data: 'harga_beli',
                                name: 'harga_beli'
                            },
                            {
                                data: 'created_at',
                                name: 'created_at'
                            },
                            {
                                data: 'updated_at',
                                name: 'updated_at'
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
            });
        </script>
    @endcan

    @can('view item-subtitute')
        <script>
            $(document).ready(() => {
                const table = $('table#item-subtitute-datatable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    ajax: '{{ route('admin.item-subtitute.index') }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'child.nama',
                            name: 'child.nama'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
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
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item')
    </script>
@endpush
