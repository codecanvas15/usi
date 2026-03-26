@extends('layouts.admin.layout.index')

@php
    $main = 'Default Coa';
@endphp

@section('title', Str::headline($main) . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline($main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view default-coa')
        <x-card-data-table title="{{ $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')

                @foreach ($data as $type => $defaul_coa_items)
                    <h4 class="fw-bold my-10">{{ Str::headline($type) }}</h4>
                    <div class="row">
                        @foreach ($defaul_coa_items as $item)
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="" class="{{ !$item->coa || $item->coa?->deleted_at ? 'text-danger' : '' }}">
                                        {{ Str::headline($item->name) }}
                                    </label>
                                    @if ($item->coa)
                                        <p>{{ $item->coa?->account_code }} - {{ $item->coa?->name }}</p>
                                    @else
                                        <p>Undefined</p>
                                    @endif
                                    <x-button color="primary" dataToggle="modal" dataTarget="#modal-log-{{ $type }}-{{ Str::snake($item->name, '-') }}" icon="clock-rotate-left" fontawesome size="sm" />
                                    <x-modal title="Coa Log Data" id="modal-log-{{ $type }}-{{ Str::snake($item->name, '-') }}" modalSize="900" headerColor="primary">
                                        <x-slot name="modal_body">
                                            @forelse ($item->default_coa_logs as $default_coa_log)
                                                <div class="border border-primary mb-10 p-5">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <span class="d-flex flex-column">
                                                                <h5 class="fw-bolder">Dari</h5>
                                                                <p class="fw-bold">{{ $default_coa_log->from_coa?->account_code }} - {{ $default_coa_log->from_coa?->name }}</p>
                                                            </span>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <span class="d-flex flex-column">
                                                                <h5 class="fw-bolder">Ke</h5>
                                                                <p class="fw-bold">{{ $default_coa_log->to_coa?->account_code }} - {{ $default_coa_log->to_coa?->name }}</p>
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        <p class="text-primary">
                                                            {{ toDayDateTimeString($default_coa_log->created_at) }} - {{ $default_coa_log->user?->name }} - {{ $default_coa_log->user?->email }}
                                                        </p>
                                                    </div>
                                                </div>

                                            @empty
                                                <p class="text-center">No data</p>
                                            @endforelse
                                        </x-slot>
                                        <x-slot name="modal_footer">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="close" />
                                        </x-slot>
                                    </x-modal>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach

                <h5 class="text-primary">Note: Jika coa merah maka data coa sudah tidak ada di master</h5>
            </x-slot>
            <x-slot name="footer">
                <x-button link="{{ route('admin.default-coa.edit') }}" color="warning" label="Edit" />
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-coa-sidebar');
        sidebarActive('#default-coa-sidebar')
    </script>
@endsection
