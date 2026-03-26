@extends('layouts.admin.layout.index')

@php
    $main = 'stock-transfer';
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
            <div class="col-md-12">
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
                                    <th>{{ Str::headline('Gudang') }}</th>
                                    <td>{{ $model->fromWarehouse->nama }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('Gudang Tujuan') }}</th>
                                    <td>{{ $model->toWarehouse->nama }}</td>
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
                                    <th>
                                        {{ Str::headline('status') }}
                                    </th>
                                    <td>
                                        <div class="d-flex gap-3">
                                            <div class="badge badge-lg badge-{{ stock_usage_status()[$model->receiving_status]['color'] }}">
                                                {{ stock_usage_status()[$model->receiving_status]['label'] }} - {{ stock_usage_status()[$model->receiving_status]['text'] }}
                                            </div>

                                            {{-- @if ($model->receiving_status == 'pending')
                                                @can("approve $main")
                                                    <x-button color="success" icon="check" fontawesome label="approve" size="sm" dataToggle="modal" dataTarget="#approve-modal" />
                                                    <x-modal title="approve purchase request" id="approve-modal" headerColor="success">
                                                        <x-slot name="modal_body">
                                                            <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                                                @csrf
                                                                <input type="hidden" name="status" value="approve">

                                                                <div class="mt-10 border-top pt-10">
                                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                                    <x-button type="submit" color="primary" label="Save data" size="sm" icon="save" fontawesome />
                                                                </div>
                                                            </form>
                                                        </x-slot>
                                                    </x-modal>
                                                @endcan

                                                @if ($model->receiving_status == 'pending')
                                                    @can("reject $main")
                                                        <x-button color="dark" icon="x" fontawesome label="reject" size="sm" dataToggle="modal" dataTarget="#reject-modal" />
                                                        <x-modal title="reject purchase request" id="reject-modal" headerColor="dark">
                                                            <x-slot name="modal_body">
                                                                <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
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
                                                @endif
                                            @endif --}}
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('created_by') }}</th>
                                    <td>{{ $model->creator->name }}</td>
                                </tr>
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
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                        </div>
                    </x-slot>

                </x-card-data-table>

                @if ($model->receiving_status == 'approve')
                    <x-card-data-table title="{{ $main . ' item' }}">
                        <x-slot name="header_content">

                        </x-slot>
                        <x-slot name="table_content">
                            <x-table>
                                <x-slot name="table_head">
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Jumlah</th>
                                    <th>Jumlah Diterima</th>
                                </x-slot>
                                <x-slot name="table_body">
                                    @foreach ($model->details as $detail)
                                        <tr>
                                            <td>{{ $loop->index + 1 }}</td>
                                            <td>{{ $detail->item->nama }}</td>
                                            <td>{{ $detail->qty }}</td>
                                            <td>{{ $detail->receiving_qty }}</td>
                                        </tr>
                                    @endforeach
                                </x-slot>
                            </x-table>
                        </x-slot>

                    </x-card-data-table>
                @else
                    <x-card-data-table title="Penerimaan Barang">
                        <x-slot name="table_content">
                            <form action="{{ route('admin.stock-transfer.update.receiving', $model->id) }}" method="post">
                                @csrf
                                @foreach ($model->details as $key => $detail)
                                    <input type="hidden" value="{{ $detail->id }}" name="detail_id[]">
                                    <div class="row" data-index="0">
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="item_{{ $key }}" class="form-label">Item</label>
                                                <input type="text" value="{{ $detail->item->nama }}" class="form-control" id="item_{{ $key }}" readonly>
                                                <input type="hidden" value="{{ $detail->item_id }}" name="item_id[]" id="item_id_{{ $key }}">
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="qty_{{ $key }}" class="form-label">
                                                    Jumlah
                                                </label>
                                                <input type="text" value="{{ $detail->qty }}" class="form-control" name="qty[]" id="qty_{{ $key }}" readonly>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="receiving_qty_{{ $key }}" class="form-label">Jumlah Diterima</label>
                                                <input type="text" value="0" class="form-control" name="receiving_qty[]" id="receiving_qty_{{ $key }}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                <div class="box-footer">
                                    <div class="d-flex justify-content-end gap-3">
                                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                                        <x-button type="submit" color="primary" label="Save data" />
                                    </div>
                                </div>
                            </form>
                        </x-slot>
                    </x-card-data-table>
                @endif
            </div>

            {{-- <div class="col-md-4">
                <x-card-data-table title="{{ 'Status Log' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        <ul class="list-group">
                            @foreach ($status_logs as $item)
                                <li class="list-group-item">
                                    <h5 class="fw-bold mb-0">From {{ Str::headline($item->from_status) }} To {{ Str::headline($item->to_status) }}</h5>
                                    <p class="mb-0">{{ Str::title($item->message) }}</p>
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ $item->created_at->diffForHumans() }}</small>
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
                                    <small class="text-secondary">{{ Str::headline($item->user->name ?? '-') }} - {{ $item->created_at->diffForHumans() }}</small>
                                </li>
                            @endforeach
                        </ul>
                    </x-slot>
                </x-card-data-table>
            </div> --}}
        </div>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#stock-sidebar');
        // sidebarMenuOpen('#master-user-sidebar');
        sidebarActive('#stock-transfer');
    </script>
@endsection
