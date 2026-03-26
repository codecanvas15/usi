@extends('layouts.admin.layout.index')

@php
    $main = 'gp-evaluation';
    $permission = 'evaluation';
    $title = 'Assessment Karyawan';
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
    <div class="row">
        <div class="col-md-9">
            <x-card-data-table title="{{ 'detail ' . $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    @include('components.validate-error')
                    <div class="row border-bottom border-primary pb-20">
                        <div class="col-md-6">
                            <p class="mb-0">Kode: {{ $model->reference }}</p>
                            <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($model->date)->translatedFormat('d F Y') }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-0">Employee: {{ $model->employee->name }}</p>
                            <p class="mb-0">Job Title: {{ $model->employee->position->nama }}</p>
                        </div>
                    </div>
                    <div class="row pt-20 pb-20">
                        <div class="col-12 table-responsive">
                            <table id="kbcTable" class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>#</th>
                                        <th colspan="2" class="text-center">Evaluation Factors</th>
                                        <th>Score</th>
                                        <th>Keterangan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->detail as $key => $detail)
                                        <tr>
                                            <td>{{ $key++ + 1 }}</td>
                                            <td>{{ ucwords(strtolower($detail->masterGpEvaluation->type)) }}</td>
                                            <td>{{ $detail->masterGpEvaluation->description }}</td>
                                            <td>{{ $detail->score }}</td>
                                            <td>{{ $detail->notes }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Total Score</td>
                                        <td class="fw-bold">
                                            <span>{{ $model->total_score }}</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td colspan="3" class="text-end fw-bold">Score (%)</td>
                                        <td class="fw-bold">
                                            <span>{{ $model->total_score }}%</span>
                                        </td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row justify-content-between pt-20 pb-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-text-area id="notes" name="notes" label="Comments and Recommendations">
                                    {{ $model->notes }}</x-text-area>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="fw-bold">Status</h4>
                            @if ($model->approval_status == 'approve')
                                <span class="badge badge-info">Approve - User Assessment has been approved.</span>
                            @elseif($model->approval_status == 'pending')
                                <span class="badge badge-warning">Pending - waiting approval</span>
                            @else
                                <span class="badge badge-dark">Reject - User Assessment rejected.</span>
                            @endif
                        </div>
                    </div>
                </x-slot>
                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                        @if ($model->approval_status == 'pending')
                            @can("delete $permission")
                                <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                            @endcan
                        @endif
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-3">
            {!! $authorization_log_view !!}
            <div class="row">
                <div class="col-md-12">
                    <x-card-data-table title="{{ 'Status Logs' }}">
                        <x-slot name="table_content">
                            <ul class="list-group">
                                @forelse ($status_logs as $item)
                                    <li class="list-group-item">
                                        @if ($item->from_status && $item->to_status)
                                            <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                                {{ Str::headline($item->to_status) }}</h5>
                                        @elseif (!$item->from_status && $item->to_status)
                                            <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                        @endif
                                        <p class="mb-0">{{ Str::title($item->message) }}</p>
                                        <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                            {{ toDayDateTimeString($item->created_at) }}</small>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <h5 class="fw-bold">Empty</h5>
                                    </li>
                                @endforelse
                            </ul>
                        </x-slot>
                    </x-card-data-table>
                    <x-card-data-table title="{{ 'Data Log' }}">
                        <x-slot name="table_content">
                            <ul class="list-group">
                                @forelse ($activity_logs as $item)
                                    <li class="list-group-item">
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                        <p class="mb-0">{{ Str::title($item->description) }}</p>
                                        <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                            {{ toDayDateTimeString($item->created_at) }}</small>
                                    </li>
                                @empty
                                    <li class="list-group-item">
                                        <h5 class="fw-bold">Empty</h5>
                                    </li>
                                @endforelse
                            </ul>
                        </x-slot>
                    </x-card-data-table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarActive('#gp-evaluation')
    </script>
@endsection
