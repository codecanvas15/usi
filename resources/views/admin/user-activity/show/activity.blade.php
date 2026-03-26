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
                            <th>{{ Str::headline('log_name') }}</th>
                            <td>{{ $activity_log->log_name }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('reference') }}</th>
                            <td>{{ $activity_log->referece?->code ?? ($activity_log->referece?->kode ?? ($activity_log->referece?->nomor_so ?? ($activity_log->referece?->nomor_po ?? $activity_log->referece?->code))) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('description') }}</th>
                            <td>{{ $activity_log->description }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('subject_type') }}</th>
                            <td>{{ $activity_log->subject_type }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('event') }}</th>
                            <td>{{ $activity_log->event }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('subject_id') }}</th>
                            <td>{{ $activity_log->subject_id }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('causer_type') }}</th>
                            <td>{{ $activity_log->causer_type }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('causer') }}</th>
                            <td>{{ $activity_log->user?->email }} - {{ $activity_log->user?->name }} - {{ $activity_log->user?->email }}</td>
                        </tr>

                    </x-slot>
                </x-table>
                <pre id="properties" class="bg-gradient-info-dark text-white">

                </pre>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')

    <script>
        $(document).ready(function() {

            $('pre#properties').html(JSON.stringify({!! $activity_log->properties !!}, null, 4));

            sidebarMenuOpen('#master-sidebar');
            sidebarMenuOpen('#master-user-sidebar');
            sidebarActive('#user-activity-sidebar');

        });
    </script>
@endsection
