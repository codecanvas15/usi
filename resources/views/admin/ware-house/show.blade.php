@extends('layouts.admin.layout.index')

@php
    $main = 'ware-house';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                <x-table theadColor='danger'>
                    <x-slot name="table_head">
                        <th></th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('nama') }}</th>
                            <td>{{ $model->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('deskripsi') }}</th>
                            <td>{!! $model->deskripsi !!}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('jalan') }}</th>
                            <td>{{ $model->jalan }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('kota') }}</th>
                            <td>{{ $model->kota }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('provinsi') }}</th>
                            <td>{{ $model->provinsi }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('negara') }}</th>
                            <td>{{ $model->country }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('zip_code') }}</th>
                            <td>{{ $model->zip_code }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('is scrap') }}</th>
                            <td>
                                <div class="badge badge-lg badge-{{ $model->is_scrap ? 'success' : 'danger' }}">
                                    {{ $model->is_scrap ? 'True' : 'False' }}
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('created_at') }}</th>
                            <td>{{ toDayDateTimeString($model->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('last medified') }}</th>
                            <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                        </tr>
                    </x-slot>
                </x-table>
            </x-slot>

            <x-slot name="footer">
                <div class="d-flex justify-content-end gap-1">
                    <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                    @can("edit $main")
                        <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                    @endcan
                    @can("delete $main")
                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />

                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                    @endcan
                </div>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="{{ $main . ' item' }}">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table id="stock-card">
                    <x-slot name="table_head">
                        <th>{{ Str::headline('#') }}</th>
                        <th>{{ Str::headline('Kode Barang') }}</th>
                        <th>{{ Str::headline('Nama') }}</th>
                        <th>{{ Str::headline('Stock Minimum') }}</th>
                        <th>{{ Str::headline('Stock') }}</th>
                    </x-slot>
                    <x-slot name="table_body">
                        {{-- @foreach ($model->stockCards() as $item)
                            <tr>
                                <td>{{ $loop->index + 1 }}</td>
                                <td><a href="{{ route('admin.stock-card.show', ['id' => $item->id, 'warehouse_id' => $model->id]) }}" class="text-primary text-decoration-underline hover_text-dark">{{ $item->kode }}</a></td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->minimum_stock }}</td>
                                <td>{{ $item->stock }}</td>
                            </tr>
                        @endforeach --}}
                    </x-slot>
                </x-table>
            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {
            const table = $('table#stock-card').DataTable({
                bDestroy: true,
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route('admin.warehouse.get-stock-card') }}' + '?warehouse_id=' + "{{ $id }}",
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'kode',
                        name: 'kode'
                    },
                    {
                        data: 'nama',
                        name: 'nama'
                    },
                    {
                        data: 'minimum_stock',
                        name: 'minimum_stock',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'stock',
                        name: 'stock',
                        orderable: false,
                        searchable: false
                    }
                ]
            });
            $('table').css('width', '100%');
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#ware-house');
    </script>
@endsection
