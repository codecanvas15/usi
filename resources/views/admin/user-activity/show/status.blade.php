@extends('layouts.admin.layout.index')

@php
    $main = 'user-activity';
    $title = 'aktivitas pengguna';
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
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline("$title") }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline("Detail $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <x-card-data-table title='{{ "detail $title" }}'>
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table>
                    <x-slot name="table_head">
                        <th></th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">
                        <tr>
                            <th>{{ Str::headline('reference model') }}</th>
                            <td>{{ $activity_status_log->reference_model }} - {{ $activity_status_log->reference_id }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('reference') }}</th>
                            <td>{{ $activity_status_log->reference?->code ?? ($activity_status_log->reference?->kode ?? ($activity_status_log->reference?->nomor_so ?? ($activity_status_log->reference?->nomor_po ?? $activity_status_log->reference?->code))) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('dari status') }}</th>
                            <td>{{ $activity_status_log->from_status }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('ke status') }}</th>
                            <td>{{ $activity_status_log->to_status }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('causer') }}</th>
                            <td>{{ $activity_status_log->user?->email }} - {{ $activity_status_log->user?->username }} - {{ $activity_status_log->user?->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('tanggal') }}</th>
                            <td>{{ toDayDateTimeString($activity_status_log->created_at) }}</td>
                        </tr>
                    </x-slot>
                </x-table>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')

    <script>
        $(document).ready(function() {
            sidebarMenuOpen('#master-sidebar');
            sidebarMenuOpen('#master-user-sidebar');
            sidebarActive('#user-activity-sidebar');
        });
    </script>
@endsection
