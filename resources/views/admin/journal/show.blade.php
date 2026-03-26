@extends('layouts.admin.layout.index')

@php
    $main = 'journal';
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

@section('content')
    @can("view $main")
        <div class="row">
            <div class="col-md-9">
                <x-card-data-table title="{{ 'detail ' . $main }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table theadColor="danger">
                            <x-slot name="table_head">
                                <th></th>
                                <th></th>
                            </x-slot>
                            @slot('table_body')
                                <tr>
                                    <td>{{ Str::headline('kode') }}</td>
                                    <td>{{ $model->code }}</td>
                                </tr>
                                @if ($model->project)
                                    <tr>
                                        <th>{{ Str::headline('project') }}</th>
                                        <td>{{ $model->project->name }}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ Str::headline('tanggal') }}</td>
                                    <td>{{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('tipe') }}</td>
                                    <td>{{ $model->journal_type }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('kurs') }}</td>
                                    <td>{{ formatNumber($model->exchange_rate) }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('currency') }}</td>
                                    <td>{{ $model->currency?->nama }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('status') }}</td>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="badge badge-lg badge-{{ journal_status()[$model->status]['color'] }}">
                                                {{ journal_status()[$model->status]['label'] }} -
                                                {{ journal_status()[$model->status]['text'] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('remark') }}</td>
                                    <td>{{ $model->remark }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('no dokumen') }}</td>
                                    <td>
                                        @if ($model->document_reference)
                                            <a href="{{ toLocalLink($model->document_reference['link']) }}" target="_blank">{{ $model->document_reference['code'] }}</a>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('referensi dokumen') }}</td>
                                    <td>
                                        @if ($model->reference)
                                            @if (isset($model->reference['link']))
                                                <a href="{{ toLocalLink($model->reference['link'] ?? '') }}" target="_blank">{{ $model->reference['code'] ?? '' }}</a>
                                            @else
                                                {!! $model->reference !!}
                                            @endif
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('created_by') }}</td>
                                    <td>{{ $model->create?->name }}</td>
                                </tr>
                                <tr>
                                    <td>{{ Str::headline('approve_by') }}</td>
                                    <td>{{ $model->approve?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_at') }}</th>
                                    <td>{{ toDayDateTimeString($model->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('last medified') }}</th>
                                    <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                                </tr>
                            @endslot
                        </x-table>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-3">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />

                            @if (!$model->is_generated && in_array($model->status, ['pending', 'revert']))
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
                <x-card-data-table title="detail journal">
                    @slot('table_content')
                        <x-table theadColor="danger">
                            @slot('table_head')
                                <th>#</th>
                                <th>{{ Str::headline('coa') }}</th>
                                <th>{{ Str::headline('debit') }}</th>
                                <th>{{ Str::headline('credit') }}</th>
                                @if (!$model->currency->is_local)
                                    <th>{{ Str::headline('debit_foreign') }}</th>
                                    <th>{{ Str::headline('credit_foreign') }}</th>
                                @endif
                                <th>{{ Str::headline('remark') }}</th>
                            @endslot
                            @slot('table_body')
                                @foreach ($model->journal_details as $item)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $item->coa->account_code }} - {{ $item->coa->name }}</td>
                                        <td class="text-end">{{ get_local_currency_symbol() }}
                                            {{ formatNumber($item->debit_exchanged) }}</td>
                                        <td class="text-end">{{ get_local_currency_symbol() }}
                                            {{ formatNumber($item->credit_exchanged) }}</td>
                                        @if (!$model->currency->is_local)
                                            @if (!$item->currency->is_local)
                                                <td class="text-end">{{ $model->currency->simbol . ' ' . formatNumber($item->debit) }}</td>
                                                <td class="text-end">{{ $model->currency->simbol . ' ' . formatNumber($item->credit) }}</td>
                                            @else
                                                <td class="text-end">-</td>
                                                <td class="text-end">-</td>
                                            @endif
                                        @endif
                                        <td>{{ $item->remark ?? '-' }}</td>
                                    </tr>
                                @endforeach

                                <tr>
                                    <td></td>
                                    <th class="text-end">Total </th>

                                    <th class="text-end">
                                        {{ get_local_currency_symbol() }}
                                        {{ formatNumber($model->journal_details->sum('debit_exchanged')) }}
                                    </th>
                                    <th class="text-end">
                                        {{ get_local_currency_symbol() }}
                                        {{ formatNumber($model->journal_details->sum('credit_exchanged')) }}
                                    </th>

                                    @if (!$model->currency->is_local)
                                        <th class="text-end">{{ $model->currency->simbol . ' ' . formatNumber($model->journal_details->where('currency_id', $model->currency->id)->sum('credit')) }}
                                        </th>
                                        <th class="text-end">{{ $model->currency->simbol . ' ' . formatNumber($model->journal_details->where('currency_id', $model->currency->id)->sum('debit')) }}
                                        </th>
                                    @endif
                                    <td></td>
                                </tr>
                            @endslot
                        </x-table>
                    @endslot
                </x-card-data-table>
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
    <script>
        $('body').addClass('sidebar-collapse')
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#journal')
    </script>
@endsection
