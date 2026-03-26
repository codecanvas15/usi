@extends('layouts.admin.layout.index')

@php
    $main = 'hrd-assessment';
    $title = 'Interview HRD';
@endphp

@section('title', Str::headline("detail $title") . ' - ')

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline("detail $title") }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-9">
                <x-card-data-table :title='"detail $title"'>
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <div class="row border-bottom border-primary pb-20">
                            <div class="col-md-3">
                                <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($model->assessment_date)->translatedFormat('d F Y') }}</p>
                                <p class="mb-0">Posisi: {{ $model->position_data->nama }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-0">Kode: {{ $model->reference }}</p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-0">Interviewer: <a href="{{ route('admin.employee.show', $model->interviewer_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->interviewer_data->name)) }}</a></p>
                            </div>
                            <div class="col-md-3">
                                <p class="mb-0">Kandidat: <a href="{{ route('admin.employee.show', $model->candidate_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->candidate_data->name)) }}</a></p>
                            </div>
                        </div>
                        <div class="row pt-20 pb-20">
                            <div class="col-md-12 table-responsive">
                                <table class="table table-striped mt-10 mb-10">
                                    <thead class="bg-dark">
                                        <tr>
                                            <th>Assessment</th>
                                            <th>Rating</th>
                                            <th>Keterangan</th>
                                    </thead>
                                    <tbody>
                                        @foreach ($model->detail as $detail)
                                            <tr>
                                                <td><b>{{ ucwords(strtolower($detail->masterHrdAssessment->title)) }}</b> - {{ $detail->masterHrdAssessment->description }}</td>
                                                <td>{{ $detail->rating }}</td>
                                                <td>{{ $detail->notes ?? '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot class="bg-dark">
                                        <tr>
                                            <td><b>Kesan dan Rekomendasi Secara Keseluruhan</b> - Ringkasan persepsi Anda tentang kekuatan/kelemahan kandidat.</td>
                                            @if ($model->assessment_status == 'y')
                                                <td class="fw-bold">Lanjut Tahap II</td>
                                            @elseif ($model->assessment_status == 'r')
                                                <td class="fw-bold">Lanjut Dengan Reservasi</td>
                                            @else
                                                <td class="fw-bold">Tidak Lanjut</td>
                                            @endif
                                            <td>{{ $model->notes ?? '-' }}</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                        <div class="row justify-content-between pb-20">
                            <div class="col-md-4">
                                <p class="mb-0"><b>Komentar</b></p>
                                <p class="mb-0">{{ $model->notes ?? '-' }}</p>
                            </div>
                            <div class="col-auto">
                                <p class="mb-0"><b>Status</b></p>
                                @if ($model->approval_status == 'approve')
                                    <span class="badge badge-info">Approve - HRD Assessment has been approved.</span>
                                @elseif($model->approval_status == 'pending')
                                    <span class="badge badge-warning">Pending - waiting approval</span>
                                @else
                                    <span class="badge badge-dark">Reject - HRD Assessment rejected.</span>
                                @endif
                            </div>
                        </div>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->status == 'pending')
                                @can("delete $main")
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
                                            <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ toDayDateTimeString($item->created_at) }}</small>
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
                                            <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ toDayDateTimeString($item->created_at) }}</small>
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
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#hrd-assessment')
    </script>
@endsection
