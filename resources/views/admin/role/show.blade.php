@extends('layouts.admin.layout.index')

@php
    $main = 'role';
    $title = 'akses';
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

            <div class="row">
                <div class="col-md-4">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('name') }}</th>
                                <td>{{ $model->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('guard_name') }}</th>
                                <td>{{ $model->name }}</td>
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
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <x-table theadColor='danger'>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->permissions->groupBy('group') as $key => $item)
                                    <tr>
                                        <th>{{ Str::headline($key) }}</th>
                                        <td>
                                            @foreach ($item as $item)
                                                {{ $item->name }},
                                            @endforeach
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </div>
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
        sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#role')
    </script>
@endsection
