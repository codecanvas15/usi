@extends('layouts.admin.layout.index')

@section('title', Str::headline("Create $title") . ' - ')

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
                        <a href='{{ route("$routeNamePrefix.index") }}'>{{ $title }}</a>
                    </li>
                    <li class="breadcrumb-item">
                        Create {{ Str::headline($title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can("create $permissionName")
        <form action="{{ route("$routeNamePrefix.store") }}" method="post">
            @csrf
            <x-card-data-table title="Create {{ $title }}">

                <x-slot name="table_content">
                    @include('components.validate-error')

                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="delivery_order_id" id="select-delivery-order-ship" label="Delivery Order Kapal" required>

                                </x-select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <x-input class="datepicker-input" name="date" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" required />
                            </div>
                        </div>
                    </div>

                    <div id="form-content">

                    </div>

                </x-slot>

                <x-slot name="footer">
                    <div class="d-flex justify-content-end gap-3">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button type="submit" color="primary" label="Save data" />
                    </div>
                </x-slot>

            </x-card-data-table>
        </form>
    @endcan
@endsection

@section('js')
    @can("create $permissionName")
        <script src="{{ asset('js/admin/select/ClosingDeliveryOrderShip.js') }}"></script>
        <script src="{{ asset('js/admin/select/coa.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>

        <script>
            $(document).ready(function() {

                let DELIVERY_ODER = null;

                const handleSelectDeliveryOrder = () => {

                    $.ajax({
                        type: "get",
                        url: `{{ route('admin.closing-delivery-order-ship.select-delivery-order.show') }}/${$('#select-delivery-order-ship').val()}`,
                        success: function({
                            data
                        }) {
                            DELIVERY_ODER = data;
                            loadFormDeliveryOrder();
                        }
                    });
                };

                const loadFormDeliveryOrder = () => {
                    let html = `
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value=${DELIVERY_ODER.branch} label="Cabang" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${DELIVERY_ODER.code}" label="Kode Delivery Order" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${DELIVERY_ODER.target_delivery}" label="Target Pengiriman" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${localDate(DELIVERY_ODER.unload_date)}" label="Tanggal Bongkar" required readonly />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" value="${DELIVERY_ODER.item_code} - ${DELIVERY_ODER.item_name}" label="Item" required readonly />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" value="${formatRupiahWithDecimal(DELIVERY_ODER.load_quantity_realization)}" label="Kuantitas Muat" required readonly />
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <x-input type="text" value="${formatRupiahWithDecimal(DELIVERY_ODER.unload_quantity_realization)}" label="Kuantitas Bongkar" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${formatRupiahWithDecimal(DELIVERY_ODER.losses_quantity)}" label="Kuantitas Hilang" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" name="note" label="Keterangan" required />
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${formatRupiahWithDecimal(DELIVERY_ODER.amount_sent)}" label="Nilai Dikirim" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" value="${formatRupiahWithDecimal(DELIVERY_ODER.amount_losses)}" label="Nilai Hilang" required readonly />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="losses_id" id="select-coa-losses" label="coa losses" required="required">

                                    </x-select>
                                </div>
                            </div>
                        </div>
                    `;

                    $('#form-content').html(html);
                    initCoaSelect("#select-coa-losses");
                };


                const init = () => {
                    initDeliveryOrderShip('#select-delivery-order-ship');

                    $('#select-delivery-order-ship').change(function(e) {
                        e.preventDefault();
                        $('#form-content').html('');
                        handleSelectDeliveryOrder();
                    });
                };

                init();
            });
        </script>
    @endcan
    <script>
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#stock-sidebar');
        sidebarActive('#closing-delivery-order-ship');
    </script>
@endsection
