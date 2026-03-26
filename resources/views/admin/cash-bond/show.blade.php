@extends('layouts.admin.layout.index')

@php
    $main = 'cash-bond';
    $title = 'kasbon';
@endphp

@section('title', Str::headline("Detail $main") . ' - ')

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
                        {{ Str::headline("Detail $title") }}
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
                <x-card-data-table title='{{ "Detail $title" }}'>
                    <x-slot name="header_content">
                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('date') }}</label>
                                    <p>{{ localDate($model->date) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('kode') }}</label>
                                    <p>{{ $model->code }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('no. bukti') }}</label>
                                    <p>{{ $model->bank_code_mutation }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('karyawan') }}</label>
                                    <p>{{ $model->employee?->name }} - {{ $model->employee?->NIK }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('branch') }}</label>
                                    <p>{{ $model->branch->name ?? '-' }}</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('project') }}</label>
                                    <p>{{ $model->project->code ?? '-' }}</p>
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
                                    <label for="">{{ Str::headline('jumlah dikembalikan') }}</label>
                                    <p>{{ $model->currency->simbol }} {{ formatNumber($model->returned_amount) }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('description') }}</label>
                                    <p>{{ $model->description ?? '-' }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="">{{ Str::headline('status') }}</label>
                                    <p>
                                    <div class="badge badge-lg badge-{{ cash_bond_status()[$model->status]['color'] }}">
                                        {{ cash_bond_status()[$model->status]['label'] }} -
                                        {{ cash_bond_status()[$model->status]['text'] }}
                                    </div>
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-20 pt-20 border-top border-primary">
                            <x-table>
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Account') }}</th>
                                    <th>{{ Str::headline('type') }}</th>
                                    <th>{{ Str::headline('amount') }}</th>
                                    <th>{{ Str::headline('note') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->cashBondDetails->whereIn('type', ['cash_bank', 'cash_advance']) as $cashBondDetail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $cashBondDetail->coa->account_code }} - {{ $cashBondDetail->coa->name }}
                                            </td>
                                            <td>{{ Str::headline($cashBondDetail->type) }}</td>
                                            <td>{{ $model->currency->simbol }}
                                                {{ $cashBondDetail->type == 'cash_bank' ? formatNumber($cashBondDetail->credit) : formatNumber($cashBondDetail->debit) }}
                                            </td>
                                            <td>{{ $cashBondDetail->note }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>

                        <div class="mt-20 pt-20">
                            <x-table>
                                <x-slot name="table_head">
                                    <th>{{ Str::headline('#') }}</th>
                                    <th>{{ Str::headline('Account') }}</th>
                                    <th>{{ Str::headline('amount') }}</th>
                                    <th>{{ Str::headline('note') }}</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->cashBondDetails->where('type', 'other') as $cashBondDetail)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $cashBondDetail->coa->account_code }} - {{ $cashBondDetail->coa->name }}
                                            </td>
                                            <td>{{ $model->currency->simbol }} {{ formatNumber($cashBondDetail->debit) }}</td>
                                            <td>{{ $cashBondDetail->note }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </div>

                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            {!! $auth_revert_void_button !!}

                            @role('super_admin')
                                @if (in_array($model->status, ['approve', 'done']) && checkAvailableDate($model->date))
                                    @include('components.generate_journal_button', ['model' => get_class($model), 'id' => $model->id, 'type' => 'cash-bond'])
                                @endif
                            @endrole
                        </div>
                    </x-slot>

                </x-card-data-table>

                @can('view journal')
                    @include('components.journal-table')
                @endcan
            </div>
            <div class="col-md-3">
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
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
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
                                    <small class="text-secondary">{{ Str::headline($item->user?->name) }} -
                                        {{ toDayDateTimeString($item->created_at) }}</small>
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
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script>
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarMenuOpen('#cash-bond-sidebar');
        sidebarActive('#cash-bond')
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\CashBond`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
