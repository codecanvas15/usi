<div class="tab-pane {{ $is_active ? 'active' : '' }}" id="{{ $tab }}" role="tabpanel">

    <x-card-data-table title="Pembayaran Customer">
        <x-slot name="header_content">
            <div class="row justify-content-between mb-4">
                <div class="row mb-4">
                    @if (get_current_branch()->is_primary)
                        <div class="col-md-2">
                            <div class="form-group">
                                <x-select id="branch-select-receivable-payment" label="branch">

                                </x-select>
                            </div>
                        </div>
                    @endif
                    <div class="col-md-3">
                        <x-select name="customer_id" id="customer_id-receivable-payment" label="Customer" onclick="initVendor()">
                        </x-select>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="from_date-receivable-payment" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-input class="datepicker-input" id="to_date-receivable-payment" name="to" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                        </div>
                    </div>
                    <div class="col-md-3 row align-self-end">
                        <div class="form-group">
                            <x-button type="button" color="info" id="set-receivable-payment-table" icon="search" fontawesome />
                            @can("create $main")
                                <x-button color="info" icon="plus" label="tambah" link="{{ route('admin.receivables-payment.create') }}" />
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table id="receivable-payment-table">
                <x-slot name="table_head">
                    <th></th>
                    <th>{{ Str::headline('#') }}</th>
                    <th>{{ Str::headline('no') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('customer') }}</th>
                    <th>{{ Str::headline('currency') }}</th>
                    <th>{{ Str::headline('total') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
</div>
