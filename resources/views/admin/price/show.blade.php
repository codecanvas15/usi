@extends('layouts.admin.layout.index')

@php
    $main = 'price';
    $title = 'harga';
@endphp

@section('title', Str::headline("Detail $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($title) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'detail ' . $title }}">
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
                        <th>{{ Str::headline('period') }}</th>
                        <td>{{ $model->period?->value }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('item') }}</th>
                        <td>{{ $model->item->nama }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('nama') }}</th>
                        <td>{{ $model->nama ?? 'Kosong' }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('harga_beli') }}</th>
                        <td>{{ formatNumber($model->harga_beli) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('harga_jual') }}</th>
                        <td>{{ formatNumber($model->harga_jual) }}</td>
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
                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
            </div>
        </x-slot>

    </x-card-data-table>

    <x-card-data-table title="{{ 'Customer ' . $title . ' ' . $model->nama }}">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                <div class="col-md-3 col-md-6 col-xl-4">
                    <x-button color="info" icon="plus" label="Create" id="create-customer-price" />
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            <div id="form-create" class="mb-30">
                <form action="{{ route('admin.price.customers.create', $model) }}" method="post">
                    @csrf
                    <div class="row justify-content-end">
                        <div class="col-md-3">
                            <x-select label="Sh no." id="sh_number_id" name="sh_number_id" required>

                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <x-input type="text" name="nama_customer" id="nama_customer" value="" required disabled />
                        </div>
                        <div class="col-md-3">
                            <x-input type="text" name="supply_point" id="supply_point" value="" required disabled />
                        </div>
                        <div class="col-md-3">
                            <x-input type="text" name="drop_point / ship to" id="drop_point" value="" required disabled />
                        </div>
                        <div class="col-md-3">
                            <x-button color='primary' fontawesome icon="save" class="w-auto float-end" size="sm" />
                        </div>
                    </div>
                </form>
            </div>
            @include('components.validate-error')
            <x-table id="price-customers">
                <x-slot name="table_head">
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('Sh no.') }}</th>
                    <th>{{ Str::headline('Customer') }}</th>
                    <th>{{ Str::headline('Drop Point') }}</th>
                    <th>{{ Str::headline('Supply Point') }}</th>
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
    <script>
        $(document).ready(() => {
            const table = $('table#price-customers').DataTable({
                processing: true,
                serverSide: true,
                responsive: true,
                ajax: '{{ route("admin.$main.customers", $model) }}',
                columns: [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'sh_number.kode',
                        name: 'sh_number.kode'
                    },
                    {
                        data: 'customer.nama',
                        name: 'customer.nama'
                    },
                    {
                        data: 'drop',
                        name: 'drop'
                    },
                    {
                        data: 'supply',
                        name: 'supply'
                    },
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    },
                ]
            });
            $('table#price-customers').css('width', '100%');
        });
    </script>

    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        $(document).ready(function() {

            $('#form-create').hide();

            $('#create-customer-price').click(function(e) {
                e.preventDefault();
                $('#form-create').fadeIn();

                initSelect2Search(`sh_number_id`, "{{ route('admin.select.sh-number') }}", {
                    id: "id",
                    text: "kode"
                });
            });

            $(`#sh_number_id`).change(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "get",
                    url: "{{ route('admin.sh-number.detail') }}/" + $(this).val(),
                    success: function({
                        data
                    }) {
                        $(`#nama_customer`).val(data.customer.nama);
                        data.sh_number_details.map((detail, key) => {
                            if (detail.type == 'Drop Point') {
                                $(`#drop_point`).val(detail.alamat);
                            }
                            if (detail.type == 'Supply Point') {
                                $(`#supply_point`).val(detail.alamat);
                            }
                        });

                        return;
                    }
                });

                return;
            });

        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-price-sidebar');
        sidebarActive('#price')
    </script>
@endsection
