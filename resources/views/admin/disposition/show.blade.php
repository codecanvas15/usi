@extends('layouts.admin.layout.index')

@php
    $main = 'disposition';
    $title = 'disposisi aset';
@endphp

@section('title', Str::headline("detail $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('asset') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('detail ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view asset-disposition')
        <div class="row">
            <div class="col-md-8">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table theadColor='danger'>
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            <x-slot name="table_body">
                                <tr>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <td>{{ $model->code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('cabang') }}</th>
                                    <td>{{ $model->branch?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('asset') }}</th>
                                    <td>{{ $model->asset?->asset_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal terakhir depresiasi') }}</th>
                                    <td>{{ localDate($model->last_journal_date) }}</td>
                                </tr>
                                @if ($model->customer)
                                    <th>{{ Str::headline('customer') }}</th>
                                    <td>{{ $model->customer->nama }}</td>
                                @endif
                                <tr>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <td>{{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tempo') }}</th>
                                    <td>{{ $model->due }} Hari</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('jatuh tempo') }}</th>
                                    <td>{{ localDate($model->due_date) }}</td>
                                </tr>
                                @if ($model->bank_internal)
                                    <tr>
                                        <th>{{ Str::headline('bank internal') }}</th>
                                        <td>{{ $model->bank_internal->nama_bank }} - {{ $model->bank_internal->no_rekening }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>{{ Str::headline('akun laba rugi') }}</th>
                                    <td>{{ $model->gain_loss_coa->account_code }} - {{ $model->gain_loss_coa->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('nilai akhir aset') }}</th>
                                    <td>{{ formatNumber($model->last_book_value) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('penjualan aset') }}</th>
                                    <td>{{ $model->is_selling_asset == 1 ? 'Ya' : 'Tidak' }}</td>
                                </tr>
                                @if ($model->is_selling_asset == 1)
                                    <tr>
                                        <th>{{ Str::headline('akun penjualan') }}</th>
                                        <td>{{ $model->selling_coa->account_code }} - {{ $model->selling_coa->name }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('faktur pajak') }}</th>
                                        <td>{{ $model->tax_number }}</td>
                                    </tr>
                                    <tr>
                                        <th>{{ Str::headline('nilai penjualan') }}</th>
                                        <td>{{ formatNumber($model->selling_price) }}</td>
                                    </tr>
                                    @if ($model->tax)
                                        <tr>
                                            <th>{{ Str::headline('pajak') }}</th>
                                            <td>{{ $model->tax->name }} ({{ $model->tax_value * 100 }}%)</td>
                                        </tr>
                                        <tr>
                                            <th></th>
                                            <td>{{ formatNumber($model->tax_amount) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <th>Total</th>
                                        <td>{{ formatNumber($model->total) }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <th>{{ Str::headline('lokasi') }}</th>
                                    <td>{{ $model->location }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('note') }}</th>
                                    <td>{{ $model->note }}</td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <div class="badge badge-lg badge-{{ fund_submission_status()[$model->status]['color'] }}">
                                            {{ fund_submission_status()[$model->status]['label'] }} -
                                            {{ fund_submission_status()[$model->status]['text'] }}
                                        </div>
                                    </td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->check_available_date)
                                @if ($model->status != 'approve' && $model->status != 'reject' && $model->status != 'void')
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
                <x-card-data-table title="{{ 'Status title' }}">
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
                <x-card-data-table title="{{ 'Data title' }}">
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
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#disposition');
    </script>
@endsection
