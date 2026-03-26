@extends('layouts.admin.layout.index')

@php
    $main = 'leave';
    $title = 'Cuti/Tidak Masuk';
@endphp

@section('title', Str::headline($title) . ' - ')

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
                        <a href="{{ route('admin.' . $main . '.index') }}">{{ $title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        Detail {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection
@section('content')
    <div class="row">

        <div class="col-md-8">
            <x-card-data-table title="detail {{ $title }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th></th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            <tr>
                                <th>{{ Str::headline('employee') }}</th>
                                <td>{{ $model->employee->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('NIK') }}</th>
                                <td>{{ $model->employee->NIK }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('divisi') }}</th>
                                <td>{{ $model->division?->name }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('cuti/izin') }}</th>
                                <td class="text-uppercase">{{ $model->type }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('cuti/izin untuk') }}</th>
                                <td class="text-uppercase">{{ $model->necessary_alias }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('alasan cuti/tidak masuk') }}</th>
                                <td class="text-uppercase">{{ $model->cause }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('keterangan') }}</th>
                                <td class="text-uppercase">{{ $model->note }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('tanggal') }}</th>
                                <td>{{ localDate($model->from_date) }} - {{ localDate($model->to_date) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Alamat selama cuti') }}</th>
                                <td>{{ $model->address }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('Nomor Hp selama cuti') }}</th>
                                <td>{{ $model->phone_number }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('status') }}</th>
                                <td>
                                    <div class="d-flex gap-3">
                                        @if ($model->status == 'pending')
                                            <div class="badge badge-lg badge-warning }}">
                                                Pending - Menunggu Persetujuan {{ $model->first_approved_by ? '2' : '1' }}
                                            </div>
                                        @elseif ($model->status == 'approve')
                                            <div class="badge badge-lg badge-info }}">
                                                Approve - Disetujui
                                            </div>
                                        @elseif ($model->status == 'change_file')
                                            <div class="badge badge-lg badge-warning }}">
                                                Ganti dokumen
                                            </div>
                                        @else
                                            <div class="badge badge-lg badge-dark }}">
                                                Reject - Ditolak
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('created_at') }}</th>
                                <td>{{ toDayDateTimeString($model->created_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('last medified') }}</th>
                                <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('File') }}</th>
                                <td>
                                    @if ($model->attachment)
                                        <a href="{{ asset('storage/' . $model->attachment) }}" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-paperclip"></i> Preview</a>
                                    @else
                                        Tidak ada file
                                    @endif
                                    @if (in_array($model->status, ['approve', 'change_file']))
                                        <button type="button" id="btn-file-attachment" class="btn btn-secondary btn-sm" onclick="checkIsHavePendingChangeFile(event)">{{ !is_null($model->changeFile?->where('status', 'pending')->first()) ? 'Lihat Perubahan File' : 'Ganti File' }}</button>
                                        <input type="file" class="d-none" id="file-attachment" onchange="handleChangeAttachment(event)">
                                        <span id="file-attachment-error"></span>

                                        <div class="modal fade" id="modal-changed-attachment" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Preview File</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div id="prview-changed-attachment">

                                                        </div>
                                                    </div>
                                                    @if (is_null($model->changeFile?->where('status', 'pending')->first()))
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            <button type="button" class="btn btn-primary" onclick="handleSubmitChangeFile(event)">Change File</button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        </x-slot>
                    </x-table>
                </x-slot>

                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-1">
                        <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                        @if ($model->status == 'pending')
                            @if ($model->check_available_date)
                                @can("edit $main")
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan

                                @can("delete $main")
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        @endif
                    </div>
                </x-slot>
            </x-card-data-table>
        </div>
        <div class="col-md-4">
            {!! $authorization_log_view !!}
            {!! $authorization_log_file_view !!}
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
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
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
                                <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} -
                                    {{ toDayDateTimeString($item->created_at) }}</small>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-card-data-table>
        </div>
    </div>
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#hrd-permission-sidebar');
        sidebarActive('#cuti');

        const fileAttachment = document.getElementById('file-attachment')

        function handleChangeAttachment(e) {
            if (e.target.files.length == 0) {
                $('#prview-changed-attachment').html('')
                return false;
            }

            $('#modal-changed-attachment').modal('show')

            const file = e.target.files[0]
            const reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = function() {
                $('#prview-changed-attachment').html(`
                    <embed src="${reader.result}" width="100%"></embed>
                `)
            }
        }

        function handleSubmitChangeFile(e) {
            // Disabled button
            e.target.setAttribute('disabled', true)
            e.target.innerHTML = 'Loading...'

            const body = document.querySelector('body')
            const file = fileAttachment.files[0]
            const formData = new FormData()
            formData.append('file_path', file)

            $.ajax({
                url: `{{ route('admin.leave.changed-file-attachment', $model->id) }}`,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(response) {
                    $('#modal-changed-attachment').modal('hide')
                    e.target.removeAttribute('disabled')
                    e.target.innerHTML = 'Change File'

                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil',
                        text: response.message
                    })

                    setTimeout(() => {
                        window.location.reload()
                    }, 750);
                },
                error: function(err) {
                    $('#modal-changed-attachment').modal('hide')

                    setTimeout(() => {
                        e.target.removeAttribute('disabled')
                        e.target.innerHTML = 'Change File'
                    }, 3000);

                    if (err.status == 422) {
                        $('#file-attachment-error').html(`
                            <div class="text-danger">${err.responseJSON.errors.file_path[0]}</div>
                        `)


                        setTimeout(() => {
                            $('#file-attachment-error').html('')
                        }, 3500);
                    }

                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal',
                        text: err.responseJSON.message
                    })
                }
            })
        }

        function checkIsHavePendingChangeFile() {
            try {
                $.get(`{{ route('admin.leave.changed-file-attachment.check-status', $model->id) }}`, {
                    _token: $('meta[name="csrf-token"]').attr('content')
                }, function(response) {
                    const {
                        data,
                        have_pending
                    } = response.data

                    if (have_pending) {
                        // Swal.fire({
                        //     icon: 'warning',
                        //     title: 'Peringatan',
                        //     text: 'Masih ada perubahan file yang belum di approve'
                        // })
                        $('#modal-changed-attachment').modal('show')

                        $('#prview-changed-attachment').html(`
                            <embed src="{{ asset('storage') }}/${data}" width="100%"></embed>
                            <div>
                                <a class="btn btn-primary" href="{{ asset('storage') }}/${data}"><i class="fa fa-paperclip"></i>Preview File</a>
                            </div>
                        `)
                    } else {
                        fileAttachment.click()
                    }
                })
            } catch (error) {
                console.log(error);
            }
        }
    </script>
@endsection
