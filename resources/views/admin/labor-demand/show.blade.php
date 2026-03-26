@extends('layouts.admin.layout.index')

@php
    $main = 'labor-demand';
    $title = 'Permintaan Tenaga Kerja';
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
                        {{ Str::headline('Detail ' . Str::headline($title)) }}
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
                <x-card-data-table title='{{ "detail $title" }}'>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('branch') }}</label>
                                    <p>{{ $model->branch->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('divisi') }}</label>
                                    <p>{{ $model->division->name }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('dibuat oleh') }}</label>
                                    <p>{{ $model->user?->name }} - {{ $model->user?->email }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('lokasi') }}</label>
                                    <p>{{ $model->location }}</p>
                                </div>
                            </div>
                            <div class="col-md-12">
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
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if ($model->status == 'pending')
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan

                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        </div>
                    </x-slot>
                </x-card-data-table>

                @foreach ($model->labor_demand_details as $labor_demand_detail)
                    <x-card-data-table title="Detail">
                        <x-slot name="table_content">

                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('posisi') }}</label>
                                        <p>{{ $labor_demand_detail->position?->nama }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('nama posisi') }}</label>
                                        <p>{{ $labor_demand_detail->position_name }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20 border-top border-primary pt-20">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('pendidikan') }}</label>
                                        <p>{{ $labor_demand_detail->education->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('jurusan') }}</label>
                                        <p>{{ $labor_demand_detail->degree->name }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('umur') }}</label>
                                        <p>{{ $labor_demand_detail->age }} tahun</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('jumlah') }}</label>
                                        <p>{{ $labor_demand_detail->quantity }} orang</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('jenis kelamin') }}</label>
                                        <p>{{ $labor_demand_detail->gender }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('lama pengalaman kerja') }}</label>
                                        <p>{{ $labor_demand_detail->long_work_experience }} tahun</p>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-20 border-top border-primary pt-20">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('pengalaman kerja') }}</label>
                                        <p>{{ $labor_demand_detail->work_experience }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('skill pegawai') }}</label>
                                        <p>{{ $labor_demand_detail->skills }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('deskripsi pekerjaan') }}</label>
                                        <p>{{ $labor_demand_detail->job_description }}</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="">{{ Str::headline('keterangan tambahan') }}</label>
                                        <p>{{ $labor_demand_detail->description }}</p>
                                    </div>
                                </div>
                            </div>

                            {{-- <div class="row mt-20 pt-20 border-top border-primary">
                                <div class="col-md-12">
                                    <div class="badge badge-lg badge-{{ labor_demand_status()[$labor_demand_detail->status]['color'] }}">
                                        {{ labor_demand_status()[$labor_demand_detail->status]['label'] }} - {{ labor_demand_status()[$labor_demand_detail->status]['text'] }}
                                    </div>
                                    @if (in_array($model->status, ['pending', 'approve', 'revert', 'partial-approve', 'partial-rejected', 'partial']))
                                        @if ($labor_demand_detail->status == 'pending' or $labor_demand_detail->status == 'revert')
                                            @can("reject $main")
                                                <x-button color="dark" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#reject-modal-{{ $labor_demand_detail->id }}" />
                                                <x-modal title="reject rekrutment detail" id="reject-modal-{{ $labor_demand_detail->id }}" headerColor="dark">
                                                    <x-slot name="modal_body">
                                                        <form action='{{ route("admin.$main.update-status.reject-labor-demand-detail", ['id' => $model, 'detail_id' => $labor_demand_detail]) }}' method="post">
                                                            @csrf
                                                            <input type="hidden" name="status" value="reject">
                                                            <div class="mt-10">
                                                                <div class="form-group">
                                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                                </div>
                                                            </div>
                                                            <div class="mt-10 border-top pt-10">
                                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                            </div>
                                                        </form>
                                                    </x-slot>
                                                </x-modal>
                                            @endcan

                                            @can("approve $main")
                                                <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal-{{ $labor_demand_detail->id }}" />
                                                <x-modal title="approve rekrutment detail" id="approve-modal-{{ $labor_demand_detail->id }}" headerColor="dark">
                                                    <x-slot name="modal_body">
                                                        <form action='{{ route("admin.$main.update-status.approve-labor-demand-detail", ['id' => $model, 'detail_id' => $labor_demand_detail]) }}' method="post">
                                                            @csrf
                                                            <input type="hidden" name="status" value="approve">
                                                            <div class="mt-10">
                                                                <div class="form-group">
                                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                                </div>
                                                            </div>
                                                            <div class="mt-10 border-top pt-10">
                                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                            </div>
                                                        </form>
                                                    </x-slot>
                                                </x-modal>
                                            @endcan
                                        @endif

                                        @if (in_array($labor_demand_detail->status, ['reject', 'approve']))
                                            @can("revert $main")
                                                <x-button color="warning" icon="x" fontawesome label="revert" size="sm" dataToggle="modal" dataTarget="#pending-modal-{{ $labor_demand_detail->id }}" />
                                                <x-modal title="revert rekrutment detail" id="pending-modal-{{ $labor_demand_detail->id }}" headerColor="warning">
                                                    <x-slot name="modal_body">
                                                        <form action='{{ route("admin.$main.update-status.revert-labor-demand-detail", ['id' => $model, 'detail_id' => $labor_demand_detail]) }}' method="post">
                                                            @csrf
                                                            <input type="hidden" name="status" value="pending">
                                                            <div class="mt-10">
                                                                <div class="form-group">
                                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                                </div>
                                                            </div>
                                                            <div class="mt-10 border-top pt-10">
                                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                            </div>
                                                        </form>
                                                    </x-slot>
                                                </x-modal>
                                            @endcan
                                            @can("close $main")
                                                <x-button color="success" icon="x" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal-{{ $labor_demand_detail->id }}" />
                                                <x-modal title="close rekrutment detail" id="close-modal-{{ $labor_demand_detail->id }}" headerColor="success">
                                                    <x-slot name="modal_body">
                                                        <form action='{{ route("admin.$main.update-status.close-labor-demand-detail", ['id' => $model, 'detail_id' => $labor_demand_detail]) }}' method="post">
                                                            @csrf
                                                            <input type="hidden" name="status" value="done">
                                                            <div class="mt-10">
                                                                <div class="form-group">
                                                                    <x-input type="text" id="message" label="message" name="message" required />
                                                                </div>
                                                            </div>
                                                            <div class="mt-10 border-top pt-10">
                                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                            </div>
                                                        </form>
                                                    </x-slot>
                                                </x-modal>
                                            @endcan
                                        @endif
                                    @endif
                                </div>
                            </div> --}}
                        </x-slot>
                    </x-card-data-table>
                @endforeach
            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @if (in_array($model->status, ['partial', 'approve', 'partial-approve', 'partial-rejected']))
                            @can("close $main")
                                <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="approve rekrutment" id="close-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="done">
                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                            <x-button color="success" icon="qrcode" fontawesome label="QR code" size="sm" dataToggle="modal" class="mt-1" dataTarget="#qr-modal" />
                            <x-modal title="QR Code Form Rekrutment" id="qr-modal" headerColor="success">
                                <x-slot name="modal_body">
                                    <x-table>
                                        <x-slot name="table_head">
                                            <th>{{ Str::headline('#') }}</th>
                                            <th>{{ Str::headline('QR') }}</th>
                                            <th>{{ Str::headline('Download') }}</th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            @forelse ($model->labor_demand_details as $labor_demand_detail)
                                                @if ($labor_demand_detail->status == 'approve')
                                                    <tr>
                                                        <td>{{ $loop->iteration }}</td>
                                                        <td>
                                                            {!! QrCode::size(250)->generate($labor_demand_detail->qr_code) !!}
                                                        </td>
                                                        <td>
                                                            <a href="data:image/png;base64, {!! base64_encode(
                                                                QrCode::format('png')->size(100)->generate($labor_demand_detail->qr_code),
                                                            ) !!}" target="_blank" download="Labor_demand_qr.png" class="btn btn-sm btn-success">download</a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @empty
                                            @endforelse
                                        </x-slot>
                                    </x-table>
                                </x-slot>
                            </x-modal>
                        @endif
                    </x-slot>
                </x-card-data-table>
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
        sidebarActive('#labor-demand');
    </script>
@endsection
