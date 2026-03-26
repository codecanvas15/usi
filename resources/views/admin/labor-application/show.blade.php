@extends('layouts.admin.layout.index')

@php
    $main = 'labor-application';
    $title = 'lamaran pekerjaan';
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
            <div class="col-md-8">
                <x-card-data-table :title='"detail $title"'>
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('Dari rekrutment') }}</label>
                                    <p>{{ $model->laborDemandDetail->labor_demand?->code }} - {{ $model->laborDemandDetail->position_name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nama') }}</label>
                                    <p>{{ $model->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('email') }}</label>
                                    <p>{{ $model->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('alamat') }}</label>
                                    <p>{{ $model->address }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('alamat domosili') }}</label>
                                    <p>{{ $model->address_domicil }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor hp') }}</label>
                                    <p>{{ $model->phone }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tanggal lahir') }}</label>
                                    <p>{{ $model->date_of_birth }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('tempat lahir') }}</label>
                                    <p>{{ $model->place_of_birth }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('agama') }}</label>
                                    <p>{{ $model->religion }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('jenis kelamin') }}</label>
                                    <p>{{ $model->gender }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status pernikahan') }}</label>
                                    <p>{{ $model->marital_status ? 'Sudah' : 'Belum' }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('nomor ktp/sim') }}</label>
                                    <p>{{ $model->identity_card_number }}</p>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dibuat_pada') }}</label>
                                    <p>{{ toDayDateTimeString($model->created_at) }}</p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="" class="d-block mb-2">QR Code</label>
                                <x-button color="primary" label="Download" link="{{ asset('storage/' . $model->application?->qr) }}" download />
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="d-flex flex-wrap gap-3">
                                        <div class="badge badge-lg badge-{{ labor_demand_status()[$model->status]['color'] }}">
                                            {{ labor_demand_status()[$model->status]['label'] }} - {{ labor_demand_status()[$model->status]['text'] }}
                                        </div>
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row pt-10 mt-10 border-top border-danger">
                            @foreach ($model->laborApplicationDocuments as $item)
                                <div class="col-md-4">
                                    <label for="">{{ Str::headline($item->type) }}</label>
                                    @if ($item->path == '')
                                        <p class="text-danger">
                                            Lengkapi Dokumen
                                        </p>
                                    @else
                                        <p>
                                            <x-button color="primary" icon="eye" fontawesome size="sm" :link="url('storage/' . $item->path)" target="blank" />
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                            @if ($model->check_available_date)
                                @if ($model->status == 'pending')
                                    <div class="col-md-6 text-end ms-auto">
                                        <x-button color='warning' fontawesome icon="edit" class="w-auto ms-auto text-end" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                    </div>
                                @endif
                            @endif

                        </div>

                        <div class="mt-10 pt-10 border-top border-primary">
                            <h4>Konta Darurat</h4>
                            <x-table>
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('nama') }}</th>
                                    <th>{{ Str::headline('hubungan') }}</th>
                                    <th>{{ Str::headline('nomor hp') }}</th>
                                    <th>{{ Str::headline('alamat') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->laborApplicationEmergencyContacts as $item)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $item->name }}</td>
                                            <td>{{ $item->relationship }}</td>
                                            <td>{{ $item->phone }}</td>
                                            <td>{{ $item->address }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>
                    </x-slot>

                </x-card-data-table>
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    @if ($item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To
                                            {{ Str::headline($item->to_status) }}</h5>
                                    @elseif (!$item->from_status && $item->to_status)
                                        <h5 class="fw-bold mb-0">{{ Str::headline($item->to_status) }}</h5>
                                    @endif
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} - {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'Data Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($activity_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">{{ Str::headline($item->event) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->description) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} - {{ toDayDateTimeString($item->created_at) }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div>
        </div>
    @endcan
@endsection

@section('js')

    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#rekrutment-sidebar');
        sidebarActive('#labor-application');
    </script>
@endsection
