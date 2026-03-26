@extends('layouts.admin.layout.index')

@php
    $main = 'fleet';
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
        <x-card-data-table title="{{ 'detail ' . $main }}" theadColor="danger">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                @include('components.validate-error')

                <x-table>
                    @slot('table_head')
                        <th></th>
                        <th></th>
                    @endslot
                    @slot('table_body')
                        @if ($model->type == 'darat' && $model->vechicle_fleet?->employee)
                            <tr>
                                <th>{{ Str::headline('pegawai') }}</th>
                                <td>{{ $model->vechicle_fleet?->employee?->name }} - {{ $model->vechicle_fleet?->employee?->NIK }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>{{ Str::headline('item') }}</th>
                            <td>{{ $model->asset?->item?->kode }} - {{ $model->asset?->item?->nama }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('asset') }}</th>
                            <td>
                                @if ($model->asset)
                                    <a class="text-primary" href="{{ route('admin.asset.show', $model->asset) }}" target="_blank" rel="noopener noreferrer">
                                        {{ $model->asset?->code }} - {{ $model->asset?->asset_name }}
                                    </a>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('nama') }}</th>
                            <td>{{ $model->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('tipe') }}</th>
                            <td>{{ $model->type }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('project') }}</th>
                            <td>{{ $model->project?->name }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('merk') }}</th>
                            <td>{{ $model->merk }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('kuantitas') }}</th>
                            <td>{{ $model->quantiry }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('tahun_pembuatan') }}</th>
                            <td>{{ $model->year }}</td>
                        </tr>

                        @if ($model->type == 'laut')
                            <tr>
                                <th>{{ Str::headline('nomor_lambung') }}</th>
                                <td>{{ $model->marine_fleet?->nomor_lambung }}</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('panjang') }}</th>
                                <td>{{ $model->marine_fleet?->panjang }} meter</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('lebar') }}</th>
                                <td>{{ $model->marine_fleet?->lebar }} meter</td>
                            </tr>
                            <tr>
                                <th>{{ Str::headline('gt') }}</th>
                                <td>{{ $model->marine_fleet?->gt }}</td>
                            </tr>
                        @endif
                        @if ($model->type == 'darat')
                            <tr>
                                <th>{{ Str::headline('tipe') }}</th>
                                <td>{{ $model->vechicle_fleet?->type }}</td>
                            </tr>
                        @endif

                        <tr>
                            <th>{{ Str::headline('created at') }}</th>
                            <td>{{ toDayDateTimeString($model->created_at) }}</td>
                        </tr>
                        <tr>
                            <th>{{ Str::headline('last modified') }}</th>
                            <td>{{ toDayDateTimeString($model->updated_at) }}</td>
                        </tr>
                    @endslot
                </x-table>
            </x-slot>

        </x-card-data-table>

        <x-card-data-table title="{{ 'dokument' }}" theadColor="danger">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table>
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>{{ Str::headline('nama dokumen') }}</th>
                        <th>{{ Str::headline('Tanggal Transaksi') }}</th>
                        <th>{{ Str::headline('Tanggal Berlaku ') }}</th>
                        <th>{{ Str::headline('Tanggal Berakhir ') }}</th>
                        <th>{{ Str::headline('Ingatkan Sebelum (hari)') }}</th>
                        <th>{{ Str::headline('status file') }}</th>
                        <th>{{ Str::headline('file') }}</th>
                        <th></th>
                    </x-slot>
                    <x-slot name="table_body">
                        @foreach ($model->fleetDocuments as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->transaction_date }}</td>
                                <td>{{ $item->effective_date }}</td>
                                <td>{{ $item->end_date }}</td>
                                <td>{{ $item->due_date }}</td>
                                <td>{{ $item->audit_result }}</td>
                                <td>
                                    <x-button :link="url('/storage') . '/' . $item->file" target color="info" icon="file" fontawesome size="sm"></x-button>
                                    <x-button color="primary" dataToggle="modal" size="sm" dataTarget="#test-modal" icon="file-lines" fontawesome />
                                    <x-modal title="Description" id="test-modal" modalSize="900">
                                        <x-slot name="modal_body">
                                            {!! $item->description !!}
                                        </x-slot>
                                        <x-slot name="modal_footer">
                                            <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" />
                                            <x-button type="button" color="primary" label="Save" />
                                        </x-slot>
                                    </x-modal>
                                </td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="{{ 'history maintenance kendaraan' }}" theadColor="danger">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table>
                    <x-slot name="table_head">
                        <th>#</th>
                        <th>Stock Usage</th>
                        <th>Tanggal</th>
                        <th>Item</th>
                        <th>Jumlah</th>
                    </x-slot>
                    <x-slot name="table_body">
                        @foreach ($stockUsageDetails as $detail)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $detail->code }}</td>
                                <td>{{ localDate($detail->date) }}</td>
                                <td>{{ $detail->item_name }} - {{ $detail->item_code }}</td>
                                <td>{{ formatNumber($detail->quantity) }} {{ $detail->unit_name }}</td>
                            </tr>
                        @endforeach
                    </x-slot>
                </x-table>
            </x-slot>
        </x-card-data-table>

        <x-card-data-table title="History Pengiriman" theadColor="danger">
            <x-slot name="header_content">

            </x-slot>
            <x-slot name="table_content">
                <x-table>
                    @slot('table_head')
                        <th>#</th>
                        <th>Ref</th>
                        <th>Tanggal Muat</th>
                        <th>Tanggal Bongkar</th>
                    @endslot
                    @slot('table_body')
                        @foreach ($model->deliveryOrders->where('status', 'done') as $key => $deliveryOrder)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td><a href="{{ route('admin.delivery-order.list-delivery-order.show', ['sale_order_id' => $deliveryOrder->so_trading_id, 'delivery_order_id' => $deliveryOrder->id]) }}" class="text-primary text-decoration-underline hover_text-dark">{{ $deliveryOrder->nomor_do }}</a></td>
                                <td>{{ \Carbon\Carbon::parse($deliveryOrder->tanggal_muat)->format('d-M-Y') }}</td>
                                <td>{{ \Carbon\Carbon::parse($deliveryOrder->tanggal_bongkar)->format('d-M-Y') }}</td>
                            </tr>
                        @endforeach
                    @endslot
                </x-table>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarMenuOpen('#master-garage-sidebar');
        sidebarActive('#fleet')
    </script>
@endsection
