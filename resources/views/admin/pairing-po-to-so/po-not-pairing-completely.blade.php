@extends('layouts.admin.layout.index')

@php
    $main = 'sales-order';
@endphp

@section('title', Str::headline('Purchase Order Not Pairing completely ') . '- ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline('Purchase Order Not Pairing completely') }}</a>
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'Purchase Order Not Pairing completely ' }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table>
                <x-slot name="table_head">
                    <tr>
                        <th>#</th>
                        <th>{{ Str::headline('nomor po') }}</th>
                        <th>{{ Str::headline('alokasi tersedia') }}</th>
                        <th>{{ Str::headline('type') }}</th>
                        <th></th>
                    </tr>
                </x-slot>
                <x-slot name="table_body">
                    @foreach ($model as $item)
                        <tr>
                            <td>{{ $loop->index + 1 }}</td>
                            <td>{{ $item->po_trading->nomor_po }}</td>
                            <td>{{ $item->jumlah - ($item->type == 'Kilo Liter' ? $item->sudah_dialokasikan / 1000 : $item->sudah_dialokasikan) }} / {{ $item->jumlah }}</td>
                            <td>{{ $item->type }}</td>
                            <td>
                                <x-button color='primary' icon='link' fontawesome size="sm" link='{{ route('admin.pairing.po_pairing', $item) }}' />
                            </td>
                        </tr>
                    @endforeach
                </x-slot>
            </x-table>

        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#trading');
        sidebarActive('#po-pairing')
    </script>
@endsection
