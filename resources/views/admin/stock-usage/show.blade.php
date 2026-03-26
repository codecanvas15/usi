@extends('layouts.admin.layout.index')

@php
    $main = 'stock-usage';
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
    @can(["view $main"])

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
                                    <th>{{ Str::headline('kantor') }}</th>
                                    <td>{{ $model->ware_house->nama }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('kode') }}</th>
                                    <td>{{ $model->code }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal') }}</th>
                                    <td>{{ localDate($model->date) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('keterangan') }}</th>
                                    <td>{{ $model->note }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('project') }}</th>
                                    <td>{{ $model->project?->name }} - {{ $model->project?->code }}</td>
                                </tr>
                                @if ($model->stock_usage_purchase_requests->count() > 0)
                                    <tr>
                                        <th>{{ Str::headline('purchase-request') }}</th>
                                        <td>
                                            @foreach ($model->stock_usage_purchase_requests as $purchase_request)
                                                <p class="my-0"><a href="{{ route('admin.purchase-request.show', $purchase_request->purchase_request_id) }}">{{ $purchase_request->purchase_request->kode }}</a></p>
                                            @endforeach
                                        </td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>{{ Str::headline('division') }}</td>
                                    <td>{{ $model->division->name ?? '-' }}</td>
                                </tr>
                                @if ($model->employee)
                                    <tr>
                                        <td>{{ Str::headline('employee') }}</td>
                                        <td>{{ $model->employee->name }} - {{ $model->employee->NIK }}</td>
                                    </tr>
                                @endif
                                @if ($model->fleet)
                                    <tr>
                                        <td>{{ Str::headline('fleet') }}</td>
                                        <td>{{ $model->fleet->name }}</td>
                                    </tr>
                                @endif
                                {{-- @if ($model->coa)
                                    <tr>
                                        <td>{{ Str::headline('coa expense') }}</td>
                                        <td>{{ $model->coa?->account_code }} - {{ $model->coa?->name }}</td>
                                    </tr>
                                @else
                                    <tr>
                                        <td>{{ Str::headline('coa expense') }}</td>
                                        <td>
                                            <select name="coa_id" id="coa-selectForm" class="form-control" required></select>
                                        </td>
                                    </tr>
                                @endif --}}
                                <tr>
                                    <th>
                                        {{ Str::headline('status') }}
                                    </th>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="badge badge-lg badge-{{ stock_usage_status()[$model->status]['color'] }}">
                                                {{ stock_usage_status()[$model->status]['label'] }} - {{ stock_usage_status()[$model->status]['text'] }}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @if ($model->file)
                                    <tr>
                                        <td>File</td>
                                        <td>
                                            <a href="{{ asset('storage/' . $model->file) }}" target="_blank"><i class="fa fa-file"></i> Lihat File</a>
                                        </td>
                                    </tr>
                                @endif
                                @can('upload-document stock-usage')
                                    @if ($model->status == 'approve')
                                        <tr>
                                            <td>{{ Str::headline('upload file') }}</td>
                                            <td>
                                                <form action="{{ route('admin.stock-usage.upload', $model) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf
                                                    <x-input type="file" name="file" required />
                                                    <x-button color="success" icon="upload" label="upload" type="submit" size="xs" />
                                                </form>
                                            </td>
                                        </tr>
                                    @endif
                                @endcan
                                <tr>
                                    <th>{{ Str::headline('created_at') }}</th>
                                    <td>{{ toDayDateTimeString($model->created_at) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('last medified') }}</th>
                                    <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>

                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            {!! $auth_revert_void_button !!}
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if (in_array($model->status, ['pending', 'revert']) && $model->check_available_date)
                                @can('edit stock-usage')
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan

                                @if ($model->status == 'pending')
                                    @can('delete stock-usage')
                                        <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                        <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                    @endcan
                                @endif
                            @endif
                        </div>
                    </x-slot>

                </x-card-data-table>

                <x-card-data-table title="{{ $main . ' item' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <x-table>
                            <x-slot name="table_head">
                                <th>#</th>
                                <th>Item</th>
                                <th>COA</th>
                                <th>Jumlah</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->stock_usage_details as $detail)
                                    <tr>
                                        <td>{{ $loop->index + 1 }}</td>
                                        <td>{{ $detail->item->nama }} - {{ $detail->item->kode }}</td>
                                        <td>
                                            @if ($detail->coa)
                                                {{ $detail->coa->account_code }} - {{ $detail->coa->name }}
                                            @endif
                                        </td>
                                        <td>{{ $detail->quantity }} {{ $detail->item->unit->name }}</td>
                                        {{-- <td>{{ $detail->total }}</td> --}}
                                    </tr>
                                @endforeach
                            </x-slot>
                        </x-table>
                    </x-slot>

                </x-card-data-table>

                @can('view journal')
                    @include('components.journal-table')
                @endcan

            </div>
            <div class="col-md-4">
                {!! $authorization_log_view !!}
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @if ($model->status == 'approve' && $model->file)
                            @can('close stock-usage')
                                <x-button color="success" icon="circle-xmark" fontawesome label="close" size="sm" dataToggle="modal" dataTarget="#close-modal" />
                                <x-modal title="close stock usage" id="close-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="done">
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <x-input type="text" name="close_note" label="Message" placeholder="Message" required />
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

                        <ul class="list-group mt-2">
                            @foreach ($status_logs as $item)
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ toDayDateTimeString($item->created_at) }}</small>
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
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-usage');
        initSelect2SearchPagination(`coa-selectForm`, `{{ route('admin.select.coa') }}`, {
            id: "id",
            text: "account_code,name"
        }, 0, {
            account_type: "Expense"
        });

        $('#coa-selectForm').change(function() {
            $.ajax({
                url: '{{ route('admin.stock-usage.coa-expense') }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    coa_id: $(this).val(),
                    id: '{{ $model->id }}'
                },
                success: function(response) {
                    console.log(response);
                },
                error: function(xhr) {
                    console.log(xhr);
                }
            });
        });
    </script>
    @can('view journal')
        <script>
            get_data_journal(`App\\Models\\StockUsage`, '{{ $model->id }}');
        </script>
    @endcan
@endsection
