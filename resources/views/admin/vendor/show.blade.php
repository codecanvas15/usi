@extends('layouts.admin.layout.index')

@php
    $main = 'vendor';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Detail ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('css')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs5/jq-3.6.0/dt-1.12.1/datatables.min.css" />
@endsection

@section('content')
    @can('view vendor')
        <div class="box">
            <div class="box-body border-0">
                <ul class="nav nav-tabs customtab2" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link rounded active" data-bs-toggle="tab" href="#general-tab" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-list"></i></span>
                            <span class="hidden-xs-down">General</span>
                        </a>
                    </li>

                    @can('view vendor-coa')
                        <li class="nav-item">
                            <a class="nav-link rounded" data-bs-toggle="tab" href="#other-tab" id="tab-pairing-btn" role="tab">
                                <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                                <span class="hidden-xs-down">Other</span>
                            </a>
                        </li>
                    @endcan
                    <li class="nav-item">
                        <a class="nav-link rounded" data-bs-toggle="tab" href="#vendor-users" id="tab-vendor-users" role="tab">
                            <span class="hidden-sm-up"><i class="fa-solid fa-link"></i></span>
                            <span class="hidden-xs-down">Vendor User</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="tab-content">
            <div class="tab-pane active" id="general-tab" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <x-card-data-table title="{{ 'detail ' . $main }}">
                            <x-slot name="header_content">

                            </x-slot>
                            <x-slot name="table_content">
                                @include('components.validate-error')
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('email') }}</label>
                                            <p>
                                                {{ $model->email }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('nama') }}</label>
                                            <p>
                                                {{ $model->nama }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('business phone') }}</label>
                                            <p>
                                                {{ $model->business_phone }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('mobile phone') }}</label>
                                            <p>
                                                {{ $model->mobile_phone }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('whatsapp') }}</label>
                                            <p>
                                                {{ $model->whatsapp }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('fax') }}</label>
                                            <p>
                                                {{ $model->fax }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('website') }}</label>
                                            <p>
                                                {{ $model->website }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('npwp') }}</label>
                                            <p>
                                                {{ $model->npwp }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('alamat') }}</label>
                                            <p>
                                                {{ $model->alamat }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('term of payment') }}</label>
                                            <p>
                                                {{ $model->term_of_payment }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('loss tolerance (%)') }}</label>
                                            <p>
                                                {{ formatNumber($model->loss_tolerance) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('created_at') }}</label>
                                            <p>
                                                {{ toDayDateTimeString($model->created_at) }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="">{{ Str::headline('last medified') }}</label>
                                            <p>
                                                {{ toDayDateTimeString($model->updated_at) }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </x-slot>
                            <x-slot name="footer">
                                <div class="d-flex justify-content-end gap-3">
                                    <x-button color='warning' label="edit" link='{{ route("admin.$main.edit", $model) }}' />
                                    <x-button color='danger' label="delete" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                </div>
                            </x-slot>

                        </x-card-data-table>

                        <x-card-data-table title="Vendor bank">
                            <x-slot name="table_content">

                                {{-- @if ($model->vendor_banks->count() < 2) --}}
                                <x-button color='info' label="create" link="{{ route('admin.vendor.create-vendor-bank', ['vendor_id' => $model]) }}" size="" icon='plus' fontawesome dataToggle="modal" class="mb-3" dataTarget="#add-bank-modal" />

                                <form action="{{ route('admin.vendor.create-vendor-bank', ['vendor_id' => $model]) }}" method="post">
                                    @csrf

                                    <x-modal title="create vendor bank" id="add-bank-modal" headerColor="info">
                                        <x-slot name="modal_body">
                                            <div class="form-group">
                                                <x-input type="text" name="bank_name" label="nama bank" required />
                                            </div>
                                            <div class="form-group">
                                                <x-input type="text" name="bank_account_number" label="nomor rekening" required />
                                            </div>
                                            <div class="form-group">
                                                <x-input type="text" name="bank_behalf_of" label="atas nama" required />
                                            </div>
                                        </x-slot>
                                        <x-slot name="modal_footer">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                            <x-button type="submit" color="primary" label="Save" />
                                        </x-slot>
                                    </x-modal>
                                </form>
                                {{-- @endif --}}

                                <x-table>

                                    <x-slot name="table_head">
                                        <th>#</th>
                                        <th>{{ Str::headline('Nama') }}</th>
                                        <th>{{ Str::headline('Nomor Rekening') }}</th>
                                        <th>{{ Str::headline('atas nama') }}</th>
                                        <th></th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                        @foreach ($model->vendor_banks as $vendor_bank)
                                            <tr>
                                                <td>{{ $loop->iteration }}</td>
                                                <td>{{ $vendor_bank->name }}</td>
                                                <td>{{ $vendor_bank->account_number }}</td>
                                                <td>{{ $vendor_bank->behalf_of }}</td>
                                                <td>
                                                    <div class="d-flex justify-content-end gap-3">
                                                        <x-button color='warning' label="" size="sm" icon='edit' fontawesome dataToggle="modal" dataTarget="#edit-modal-{{ $loop->index }}" />

                                                        <form action="{{ route('admin.vendor.update-vendor-bank', ['vendor_id' => $model, 'bank_id' => $vendor_bank]) }}" method="post">
                                                            @csrf
                                                            @method('PUT')
                                                            <x-modal title="Edit vendor model" id="edit-modal-{{ $loop->index }}" headerColor="warning">
                                                                <x-slot name="modal_body">
                                                                    <div class="form-group">
                                                                        <x-input type="text" name="bank_name" label="nama bank" value="{{ $vendor_bank->name }}" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <x-input type="text" name="bank_account_number" label="nomor rekening" value="{{ $vendor_bank->account_number }}" required />
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <x-input type="text" name="bank_behalf_of" label="atas nama" value="{{ $vendor_bank->behalf_of }}" required />
                                                                    </div>
                                                                </x-slot>
                                                                <x-slot name="modal_footer">
                                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                                                    <x-button type="submit" color="primary" label="Save" />
                                                                </x-slot>
                                                            </x-modal>
                                                        </form>

                                                        <x-button color='danger' label="" dataToggle='modal' dataTarget='#delete-bank-modal-{{ $model->id }}-{{ $loop->index }}' size="sm" icon='trash' fontawesome />
                                                        <div class="modal fade" id="delete-bank-modal-{{ $model->id }}-{{ $loop->index }}" tabindex="-1" aria-labelledby="" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content rounded">
                                                                    <div class="modal-header bg-danger">
                                                                        <h5 class="modal-title">{{ Str::headline('Are you sure to do this action ?') }}</h5>
                                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                                    </div>
                                                                    <form action="{{ route('admin.vendor.destroy-vendor-bank', ['vendor_id' => $model->id, 'bank_id' => $vendor_bank->id]) }}" method="post">
                                                                        @csrf
                                                                        @method('delete')

                                                                        <div class="modal-body">
                                                                            <p>You'll lose your data, this action can't be undone.</p>
                                                                        </div>

                                                                        <div class="modal-footer">
                                                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                                                            <x-button type="submit" color="danger" label="Delete" />
                                                                        </div>

                                                                    </form>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </x-slot>
                                </x-table>

                            </x-slot>
                        </x-card-data-table>
                    </div>
                </div>
            </div>

            @can('view vendor-coa')
                <div class="tab-pane" id="other-tab" role="tabpanel">
                    <div class="row">
                        <div class="col-md-12">
                            <x-card-data-table title="{{ 'detail ' . $main }}">
                                <x-slot name="header_content">

                                </x-slot>
                                <x-slot name="table_content">
                                    <x-table theadColor='danger' id="custmer-coas">
                                        <x-slot name="table_head">
                                            <th>Vendor Coa</th>
                                            <th></th>
                                        </x-slot>
                                        <x-slot name="table_body">
                                            @if ($model->vendor_coas->count() > 0)
                                                @foreach ($model->vendor_coas as $item)
                                                    <tr class="{{ $item->coa->deleted_at != null ? 'text-danger' : '' }}">
                                                        <th>{{ Str::headline($item->type) }}</th>
                                                        <td>{{ $item->coa->account_code }} - {{ $item->coa->name }}</td>
                                                    </tr>
                                                @endforeach
                                            @else
                                                @foreach (vendor_coa_types() as $item)
                                                    <tr>
                                                        <th>{{ Str::headline($item) }}</th>
                                                        <td>No Data</td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </x-slot>
                                    </x-table>
                                    <h5 class="text-primary">Note: Jika data coa merah maka data coa sudah tidak ada di master coa</h5>
                                </x-slot>
                                <x-slot name="footer">
                                    <div class="d-flex justify-content-end gap-3">
                                        <x-button color='secondary' label="cancel" link='{{ route("admin.$main.index") }}' />
                                        <x-button color='warning' label="edit" link='{{ route("admin.$main.edit", $model) }}' />
                                    </div>
                                </x-slot>

                            </x-card-data-table>
                        </div>
                    </div>
                </div>
            @endcan

            <div class="tab-pane" id="vendor-users" role="tabpanel">
                <div class="row">
                    <div class="col-md-12">
                        <x-card-data-table title="{{ $main . ' Users' }}">
                            <x-slot name="header_content">
                                <div class="row justify-content-between mb-4">
                                    <div class="col-md-12">
                                        <x-button color="info" icon="plus" label="Create" dataToggle="modal" dataTarget="#create-modal" />
                                        <x-modal title="create new data" id="create-modal" headerColor="info">
                                            <x-slot name="modal_body">
                                                <form action="{{ route('admin.vendor.users.store', $model) }}" method="post" enctype="multipart/form-data">
                                                    @csrf
                                                    <div class="form-group">
                                                        <x-input type="text" label="name" name="name" required />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="email" label="email" name="email" required />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="password" label="password" name="password" required />
                                                    </div>
                                                    <div class="form-group">
                                                        <x-input type="password" label="confirm_password" name="confirm_password" required />
                                                    </div>
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                                    <x-button type="submit" color="primary" label="Save data" />
                                                </form>
                                            </x-slot>
                                        </x-modal>
                                    </div>
                                </div>
                            </x-slot>
                            <x-slot name="table_content">
                                <x-table theadColor='danger' id="vendor-users-table">
                                    <x-slot name="table_head">
                                        <th>{{ Str::upper('#') }}</th>
                                        <th>{{ Str::upper('Email') }}</th>
                                        <th>{{ Str::upper('Name') }}</th>
                                        <th>{{ Str::upper('Created At') }}</th>
                                        <th>{{ Str::upper('Last Modified At') }}</th>
                                        <th>{{ Str::upper('action') }}</th>
                                    </x-slot>
                                    <x-slot name="table_body">
                                    </x-slot>
                                </x-table>
                            </x-slot>

                        </x-card-data-table>
                    </div>
                </div>
            </div>

        </div>
    @endcan
@endsection

@section('js')
    <script type="text/javascript" src="{{ asset('js/datatable/datatables.min.js') }}"></script>
    <script>
        $(document).ready(() => {

            let vendor_loaded = false;
            $('#tab-vendor-users').click(function(e) {
                e.preventDefault();
                if (vendor_loaded) return;
                const table = $('table#vendor-users-table').DataTable({
                    processing: true,
                    serverSide: true,
                    columnDefs: [{
                            "width": "10%",
                            "targets": 1
                        },
                        {
                            "width": "20%",
                            "targets": 3
                        },
                        {
                            "width": "20%",
                            "targets": 4
                        },
                    ],
                    ajax: '{{ route("admin.$main.users", $model) }}',
                    columns: [{
                            data: 'DT_RowIndex',
                            name: 'DT_RowIndex',
                            orderable: false,
                            searchable: false
                        },
                        {
                            data: 'email',
                            name: 'email'
                        },
                        {
                            data: 'name',
                            name: 'name'
                        },
                        {
                            data: 'created_at',
                            name: 'created_at'
                        },
                        {
                            data: 'updated_at',
                            name: 'updated_at'
                        },
                        {
                            data: 'action',
                            name: 'action'
                        },
                    ]
                });
                $('table').css('width', '100%');

            });
        });
    </script>
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-vendor-sidebar');
        sidebarActive('#vendor-sidebar');
    </script>
@endsection
