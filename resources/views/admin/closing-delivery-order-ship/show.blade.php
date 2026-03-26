@extends('layouts.admin.layout.index')

@section('title', Str::headline("Detail $title") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("$routeNamePrefix.index") }}">{{ Str::headline($title) }}</a>
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
    @can("view $permissionName")
        <x-card-data-table title="Detail {{ $title }}">
            <x-slot name="table_content">

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Cabang</label>
                            <p>{{ $model->branch->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Delivery Order</label>
                            <p>{{ $model->deliveryOrder->code }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal</label>
                            <p>{{ localDate($model->date) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Target Pengiriman</label>
                            <p>{{ localDate($model->deliveryOrder->target_delivery) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Tanggal Bongkar</label>
                            <p>{{ localDate($model->deliveryOrder->unload_date) }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Item</label>
                            <p>{{ $model->item->kode }} - {{ $model->item->nama }}</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Kuantitas Muat</label>
                            <p>{{ formatNumber($model->deliveryOrder->load_quantity_realization) }}</p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="">Kuantitas Bongkar</label>
                            <p>{{ formatNumber($model->deliveryOrder->load_quantity_realization) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Kuantitas Hilang</label>
                            <p>{{ formatNumber($model->losses_quantity) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Keterangan</label>
                            <p>{{ $model->note }}</p>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Nilai Dikirim</label>
                            <p>{{ formatNumber($model->amount_sent) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Nilai Hilang</label>
                            <p>{{ formatNumber($model->amount_losses) }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Coa Losses</label>
                            <p>{{ $model->lossesCoa->name }} - {{ $model->lossesCoa->account_code }}</p>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="">Status</label>
                            <p>
                            <div class="badge badge-lg badge-{{ closing_delivery_order_ship()[$model->status]['color'] }}">
                                {{ Str::headline(closing_delivery_order_ship()[$model->status]['label']) }}
                            </div>

                            @if ($model->status != 'void')
                                @can("void $permissionName")
                                    <x-button color="danger" icon="trash" fontawesome label="void" size="sm" dataToggle="modal" dataTarget="#void-modal" />
                                    <x-modal title="void purcase order general" id="void-modal" headerColor="danger">
                                        <x-slot name="modal_body">
                                            <form action='{{ route("$routeNamePrefix.void", $model) }}' method="post">
                                                @csrf
                                                <input type="hidden" name="status" value="void">
                                                <div class="mt-10 border-top pt-10">
                                                    <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" size="sm" icon="times" fontawesome />
                                                    <x-button type="submit" color="primary" label="void" size="sm" icon="save" fontawesome />
                                                </div>
                                            </form>
                                        </x-slot>
                                    </x-modal>
                                @endcan
                            @endif
                            </p>
                        </div>
                    </div>
                </div>

            </x-slot>

        </x-card-data-table>
    @endcan
@endsection

@section('js')
    <script>
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#closing-delivery-order-ship');
    </script>
@endsection
