@extends('layouts.admin.layout.index')

@php
    $main = 'delivery-order';
@endphp

@section('title', Str::headline('Detail delivery order') . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("transport.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("transport.$main.show", $model) }}">{{ Str::headline('detail purchase transport') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail delivery order ') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'Detail delivery order  ' }}">
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
                        <th>{{ Str::headline('nomor_do') }}</th>
                        <td>{{ $data->code }}</td>
                    </tr>
                    @foreach ($data->sh_number->sh_number_details as $item)
                        <tr>
                            <th>{{ Str::headline($item->type) }}</th>
                            <td>
                                {{ $item->alamat }}
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td>{{ Str::headline('nama driver ') }}</td>
                        <td>{{ $model->driver_name }}</td>
                    </tr>
                    <tr>
                        <td>{{ Str::headline('nomor hp driver ') }}</td>
                        <td>{{ $model->driver_phone }}</td>
                    </tr>
                    <tr>
                        <td>{{ Str::headline('informasi kendaran') }}</td>
                        <td>{{ $model->vehicle_information }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('sh no.') }}</th>
                        <td>{{ $data->sh_number->kode }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('target delivery') }}</th>
                        <td>{{ $data->target_delivery }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('tanggal muat') }}</th>
                        <td>{{ localDate($data->load_date) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('tanggal bongkar') }}</th>
                        <td>{{ localDate($data->unload_date) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('kuantitas kirim') }}</th>
                        <td>{{ formatNumber($data->load_quantity) }} {{ $model-></td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('realisasi kuantitas kirim') }}</th>
                        <td>{{ formatNumber($data->load_quantity_realization) }} {{ $model-></td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('realsasi kuantitas diterima') }}</th>
                        <td>{{ formatNumber($data->unload_quantity_realization) }} {{ $model-></td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('status') }}</th>
                        <td>
                            <div class="badge badge-lg badge-{{ get_delivery_order_status()[$data->status]['color'] }}">
                                {{ get_delivery_order_status()[$data->status]['label'] }} - {{ get_delivery_order_status()[$data->status]['text'] }}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('file') }}</th>
                        <td>
                            @if ($data->file)
                                <x-button type="button" color="info" label="file" size="sm" icon="file" label="view_file" link='{{ url("storage/$data->file") }}' fontawesome />
                            @else
                                <x-button badge color="danger" icon="eye-slash" size="sm" label="file not available" fontawesome />
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('created_at') }}</th>
                        <td>{{ toDayDateTimeString($data->created_at) }}</td>
                    </tr>
                    <tr>
                        <th>{{ Str::headline('last medified') }}</th>
                        <td>{{ toDayDateTimeString($data->updated_at) }}</td>
                    </tr>
                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarActive('#transport-delivery-order')
    </script>
@endsection
