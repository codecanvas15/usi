@extends('layouts.admin.layout.index')

@php
    $main = 'user-assessment';
    $title = 'Interview User';
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
                    @php
                        $kbc_percentage_total = 0;
                        $ksc_percentage_total = 0;
                    @endphp
                    <div class="row border-bottom border-primary pb-20">
                        <div class="col-md-3">
                            <p class="mb-0">Tanggal: {{ \Carbon\Carbon::parse($model->assessment_date)->translatedFormat('d F Y') }}</p>
                            <p class="mb-0">Job Title: {{ $model->candidate_data->laborDemandDetail->position->nama }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0">Kode: {{ $model->reference }}</p>
                            <p class="mb-0">Department Name: {{ $model->candidate_data->laborDemandDetail->labor_demand->division->name }}</p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0">Kandidat: <a href="{{ route('admin.labor-application.show', $model->candidate_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->candidate_data->name)) }}</a></p>
                            <p class="mb-0">Interviewer: <a href="{{ route('admin.employee.show', $model->interviewer_data->id) }}" class="text-primary text-decoration-underline hover_text-dark">{{ ucwords(strtolower($model->interviewer_data->name)) }}</a></p>
                        </div>
                        <div class="col-md-3">
                            <p class="mb-0">Hiring Manager: {{ ucwords(strtolower($model->hiring_manager)) }}</p>
                        </div>
                    </div>
                    <div class="row pt-20 pb-20">
                        <div class="col-12">
                            <h4><b>{{ Str::headline('Key Behavioral Competencies') }}</b></h4>
                        </div>
                        <div class="col-12 table-responsive">
                            <table id="kbcTable" class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Wts</th>
                                        <th>Rating 1-5 (5 Highest)</th>
                                        <th>Weighted Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->detail as $key => $detail)
                                        @if ($detail->type == 'kbc')
                                            @php
                                                $kbc_percentage_total += $detail->masterUserAssessment->weight * 100;
                                            @endphp
                                            <tr>
                                                <td>{{ ucwords(strtolower($detail->masterUserAssessment->name)) }}</td>
                                                <td>{{ $detail->masterUserAssessment->weight * 100 }}%</td>
                                                <td>{{ formatRating($detail->rating) }}</td>
                                                <td>{{ $detail->weight }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <td class="text-end fw-bold">Overall Behavioral Competency Rating</td>
                                        <td class="fw-bold">
                                            <span>{{ $kbc_percentage_total }}%</span>
                                        </td>
                                        <td></td>
                                        <td class="fw-bold">
                                            <div>
                                                <span class="fw-bold">{{ $model->behavioral_rating }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row pt-20 pb-20">
                        <div class="col-12">
                            <h4><b>{{ Str::headline('Key Skill Competencies') }}</b></h4>
                        </div>
                        <div class="col-12 table-responsive">
                            <table id="kscTable" class="table table-striped mt-10 mb-10">
                                <thead class="bg-dark">
                                    <tr>
                                        <th>Name</th>
                                        <th>Wts</th>
                                        <th>Rating 1-5 (5 Highest)</th>
                                        <th>Weighted Score</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($model->detail as $key => $detail)
                                        @if ($detail->type == 'ksc')
                                            @php
                                                $ksc_percentage_total += $detail->masterUserAssessment->weight * 100;
                                            @endphp
                                            <tr>
                                                <td>{{ ucwords(strtolower($detail->masterUserAssessment->name)) }}</td>
                                                <td>{{ $detail->masterUserAssessment->weight * 100 }}%</td>
                                                <td>{{ formatRating($detail->rating) }}</td>
                                                <td>{{ $detail->weight }}</td>
                                            </tr>
                                        @endif
                                    @endforeach
                                </tbody>
                                <tfoot class="bg-dark">
                                    <tr>
                                        <td class="text-end fw-bold">Overall Skill Competency Rating</td>
                                        <td class="fw-bold">
                                            <div>
                                                <span>{{ $ksc_percentage_total }}%</span>
                                            </div>
                                        </td>
                                        <td></td>
                                        <td>
                                            <div>
                                                <span class="fw-bold">{{ $model->skill_rating }}</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="row pt-20 pb-20">
                        <div class="col-md-4">
                            <p class="mb-0"><b>What Impressed You The Most</b></p>
                            <p class="mb-0">{{ $model->first_note ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><b>What Impressed You The Least</b></p>
                            <p class="mb-0">{{ $model->second_note ?? '-' }}</p>
                        </div>
                        <div class="col-md-4">
                            <p class="mb-0"><b>What Questions Or Reservations Do You Have?</b></p>
                            <p class="mb-0">{{ $model->third_note ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="row g-0 mt-30">
                        <div class="col-md-4">
                            <div class="row">
                                <div class="col-auto">
                                    <h4 class="fw-bold mb-0">Overall Rating</h4>
                                    <p class="mt-3 mb-0">Ratings:</p>
                                    <ul>
                                        <li>5. Excellent</li>
                                        <li>4. Good</li>
                                        <li>3. Fair</li>
                                        <li>2. Poor</li>
                                        <li>1. Unacceptable</li>
                                    </ul>
                                </div>
                                <div class="col-auto">
                                    <h5 class="fw-bold text-danger mb-0">{{ $model->total_rating }}</h5>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <h4 class="fw-bold">Hiring Recommendation</h4>
                            <p class="mb-0">{{ formatRecommendStatus($model->recommend_status) }}</p>
                        </div>
                        <div class="col-md-12">
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
            <div class="row">
                <div class="col-md-12">
                    {!! $authorization_log_view !!}
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
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#user-assessment')
    </script>
@endsection
