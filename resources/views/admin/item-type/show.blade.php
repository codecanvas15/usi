@extends('layouts.admin.layout.index')

@php
    $main = 'item-type';
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
    <x-card-data-table title="{{ 'detail ' . $main }}">
        <x-slot name="header_content">

        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')
            <div class="row">
                <div class="col-md-4">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th>Item Type Information</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('Nama') }}</th>
                                <td>{{ $model->nama }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('created_at') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('updated_at') }}</th>
                                <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                            </tr>
                        </x-slot>
                    </x-table>
                </div>
                <div class="col-md-8">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th>Coa Binding</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->item_type_coas ?? item_type_coas()[$model->nama] as $item)
                                <tr class="{{ $item->coa->deleted_at ? 'text-danger' : '' }}">
                                    <th>{{ Str::headline($item->type) }}</th>
                                    <td>{{ $item->coa?->name }} - {{ $item->coa?->account_code }}</td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                    <span>* Jika merah, coa sudah tidak ada di master</span>
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <div class="d-flex justify-content-end gap-1">
                <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
            </div>
        </x-slot>

    </x-card-data-table>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-item-sidebar');
        sidebarActive('#item-type')
    </script>
@endsection
