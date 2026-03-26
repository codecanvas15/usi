@extends('layouts.admin.layout.index')

@php
    $main = 'degree';
    $title = 'Jurusans';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline('Jurusan') }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail Jurusan') }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title="{{ 'detail Jurusan' }}">
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
                            <th>{{ Str::headline('dibuat pada') }}</th>
                            <td>{{ toDayDateTimeString($model->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('diubah pada') }}</th>
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
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-employee-sidebar');
        sidebarActive('#degree-sidebar')
    </script>
@endsection
