@extends('layouts.admin.layout.index')

@php
    $main = 'asset';
    $menu = 'aktiva tetap';
@endphp

@section('title', Str::headline("Detail Master $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('master') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($menu) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view master-asset')
        <div class="row">
            <div class="col-md-7">
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
                                    <th>{{ Str::headline('cabang') }}</th>
                                    <td>{{ $model->branch?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('kategori') }}</th>
                                    <td>{{ $model->item_category?->nama }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('nama aset') }}</th>
                                    <td>{{ $model->asset_name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('divisi') }}</th>
                                    <td>{{ $model->division?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal pembelian') }}</th>
                                    <td>{{ $model->purchase_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal pemakaian') }}</th>
                                    <td>{{ $model->usage_date }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('lokasi awal aset') }}</th>
                                    <td>{{ $model->initial_location }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('kategori asset') }}</th>
                                    <td>{{ $model->assetCategory?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('kategori asset dokumen') }}</th>
                                    <td>{{ $model->assetDocumentType?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('note') }}</th>
                                    <td>{{ $model->note }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                    <x-slot name="footer">
                        <div class="d-flex justify-content-end gap-1">
                            <x-button color='secondary' fontawesome icon="backward" class="w-auto" size="sm" link='{{ route("admin.$main.index") }}' />
                            @if ($model->status != 'inactive')
                                @can('edit master-asset')
                                    <x-button color='warning' fontawesome icon="edit" class="w-auto" size="sm" link='{{ route("admin.$main.edit", $model) }}' />
                                @endcan
                                @can('delete master-asset')
                                    <x-button color='danger' fontawesome icon="trash" class="w-auto" size="sm" dataToggle='modal' dataTarget='#delete-modal-{{ $model->id }}' />
                                    <x-modal-delete id="delete-modal-{{ $model->id }}" url='{{ "admin.$main.destroy" }}' dataId="{{ $model->id }}" />
                                @endcan
                            @endif
                        </div>
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'depresiasi aset' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        @include('components.validate-error')
                        <x-table theadColor='danger'>
                            <x-slot name="table_head">
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Ket.</th>
                            </x-slot>
                            <x-slot name="table_body">
                                @foreach ($model->depreciations as $depreciation)
                                    <tr>
                                        <td>
                                            {{ localDate($depreciation->date) }}
                                        </td>
                                        <td class="text-end">
                                            {{ formatNumber($depreciation->amount) }}
                                        </td>
                                        <td>
                                            {{ $depreciation->note }}
                                        </td>
                                    </tr>
                                @endforeach
                            </x-slot>
                            <x-slot name="table_foot">
                                <tr>
                                    <th>{{ Str::headline('NILAI ASET') }}</th>
                                    <th class="text-end">{{ formatNumber($model->value) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('JUMLAH PENYUSUTAN') }}</th>
                                    <th class="text-end">{{ formatNumber($model->depreciations->sum('amount')) }}</th>
                                    <th></th>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('SISA NILAI') }}</th>
                                    <th class="text-end">{{ formatNumber($model->outstanding_value) }}</th>
                                    <th></th>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>
            </div>
            <div class="col-md-5">
                <x-card-data-table title="{{ 'Action' }}">
                    <x-slot name="header_content">

                    </x-slot>
                    <x-slot name="table_content">
                        {{-- ================================= APPROVE | PARTIAL APPROVE | PARTIAL REJECT | REJECT | REVERT  ======================================== --}}
                        @if ($model->status != 'cancel' && $model->status != 'inactive' && $model->depreciations->count() == 0)
                            @can('delete master-asset')
                                <x-button color="danger" icon="times" fontawesome label="Batalkan Asset" size="sm" dataToggle="modal" dataTarget="#cancel-modal" />
                                <x-modal title="batalkan asset" id="cancel-modal" headerColor="success">
                                    <x-slot name="modal_body">
                                        <form action='{{ route("admin.$main.update-status", $model) }}' method="post">
                                            @csrf
                                            <input type="hidden" name="status" value="cancel">

                                            <div class="mt-10 border-top pt-10">
                                                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                <x-button type="submit" color="primary" label="Lanjutkan" size="sm" icon="save" fontawesome />
                                            </div>
                                        </form>
                                    </x-slot>
                                </x-modal>
                            @endcan
                        @endif
                        {{-- ================================= APPROVE | PARTIAL APPROVE | PARTIAL REJECT | REJECT | REVERT  ======================================== --}}
                    </x-slot>
                </x-card-data-table>
                <x-card-data-table title="{{ 'depresiasi aset' }}">
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
                                    <th>{{ Str::headline('nilai perolehan') }}</th>
                                    <td>{{ formatNumber($model->value) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('nilai residu') }}</th>
                                    <td>{{ formatNumber($model->residual_value) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('umur depresiasi') }}</th>
                                    <td>{{ formatNumber($model->estimated_life) }} bulan</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('tanggal akhir depresiasi') }}</th>
                                    <td>{{ localDate($model->depreciation_end_date) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('akun aset') }}</th>
                                    <td>{{ $model->asset_coa?->account_code }} - {{ $model->asset_coa?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('akun akm. penyusutan') }}</th>
                                    <td>{{ $model->acumulated_depreciation_coa?->account_code }} -
                                        {{ $model->acumulated_depreciation_coa?->name }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('akun penyusutan') }}</th>
                                    <td>{{ $model->depreciation_coa?->account_code }} - {{ $model->depreciation_coa?->name }}
                                    </td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('nilai depresiasi') }}</th>
                                    <td>{{ formatNumber($model->depreciation_value) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('nilai buku') }}</th>
                                    <td>{{ formatNumber($model->book_value) }}</td>
                                </tr>
                                <tr>
                                    <th>{{ Str::headline('akumulasi depresiasi') }}</th>
                                    <td>{{ formatNumber($model->acumulated_depreciation) }}</td>
                                </tr>
                            </x-slot>
                        </x-table>
                    </x-slot>
                </x-card-data-table>

                @if ($model->item_receiving_report_detail)
                    <x-card-data-table title="{{ 'referensi aset' }}">
                        <x-slot name="header_content">

                        </x-slot>
                        <x-slot name="table_content">
                            @include('components.validate-error')
                            @php
                                if ($model->item_receiving_report_detail->item_receiving_report->tipe == 'jasa') {
                                    $type = 'item-receiving-report-service';
                                } elseif ($model->item_receiving_report_detail->item_receiving_report->tipe == 'general') {
                                    $type = 'item-receiving-report-general';
                                } elseif ($model->item_receiving_report_detail->item_receiving_report->tipe == 'trading') {
                                    $type = 'item-receiving-report-trading';
                                } elseif ($model->item_receiving_report_detail->item_receiving_report->tipe == 'transport') {
                                    $type = 'item-receiving-report-transport';
                                }
                            @endphp
                            <x-table theadColor='danger'>
                                <x-slot name="table_head">
                                    <th></th>
                                    <th></th>
                                </x-slot>
                                <x-slot name="table_body">
                                    <tr>
                                        <th>{{ Str::headline('no. lpb') }}</th>
                                        <td><a href="{{ route('admin.' . $type . '.show', $model->item_receiving_report_detail?->item_receiving_report_id) }}" target="_blank">{{ $model->item_receiving_report_detail?->item_receiving_report?->kode }}</a>
                                        </td>
                                    </tr>
                                </x-slot>
                            </x-table>
                        </x-slot>
                    </x-card-data-table>
                @endif
            </div>
        </div>

        <div class="mt-20">
            <x-card-data-table title="{{ 'asset dokumen' }}">
                <x-slot name="table_content">
                    <x-table theadColor='danger'>
                        <x-slot name="table_head">
                            <th>#</th>
                            <th>{{ Str::headline('Nama Dokumen') }}</th>
                            <th>{{ Str::headline('Tanggal Transaksi') }}</th>
                            <th>{{ Str::headline('Tanggal Berlaku') }}</th>
                            <th>{{ Str::headline('Tanggal Berakhir ') }}</th>
                            <th>{{ Str::headline('Reminder (hari)') }}</th>
                            <th>{{ Str::headline('status file') }}</th>
                            <th>{{ Str::headline('file') }}</th>
                            <th>{{ Str::headline('reminder exp') }}</th>
                            <th></th>
                        </x-slot>
                        <x-slot name="table_body">
                            @foreach ($model->assetDocuments as $item)
                                @php
                                    if ($item->end_date && $item->due_date) {
                                        $reminder_badge = \Carbon\Carbon::parse($item->end_date)->subDays($item->due_date)->format('Y-m-d') <= \Carbon\Carbon::now()->format('Y-m-d') ? true : false;

                                        $expiredCounter = (int) \Carbon\Carbon::now()->format('d') - (int) \Carbon\Carbon::parse($item->end_date)->format('d');
                                    } else {
                                        $reminder_badge = false;
                                        $expiredCounter = 0;
                                    }
                                @endphp
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
                                    <td>
                                        {!! $reminder_badge ? ($expiredCounter < 0 ? '<span class="badge badge-warning">Expired ' . $expiredCounter . ' Hari</span>' : '<span class="badge badge-danger">Expired</span>') : '' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </x-slot>
                    </x-table>
                </x-slot>
            </x-card-data-table>
        </div>
    @endcan
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>

    <script>
        sidebarMenuOpen('#master-sidebar');
        sidebarActive('#master-asset-sidebar');
        sidebarActive('#asset-sidebar');
    </script>
@endsection
