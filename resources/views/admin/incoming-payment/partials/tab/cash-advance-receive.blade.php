<div class="tab-pane {{ $is_active ? 'active' : '' }}" id="{{ $tab }}" role="tabpanel">

    <x-card-data-table title="Penerimaan Deposit">
        <x-slot name="header_content">
            <div class="row mb-4">
                @if (get_current_branch()->is_primary)
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select id="branch-select-cash-advance-receive" label="branch">

                            </x-select>
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="from_date-cash-advance-receive" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="to_date-cash-advance-receive" name="to" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-3 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="set-cash-advance-receive-table" icon="search" fontawesome />
                        <x-button color="info" icon="plus" label="Tambah" link="{{ route('admin.cash-advance-receive.create') }}" />
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table id="cash-advance-receive-table">
                <x-slot name="table_head">
                    <th></th>
                    <th>#</th>
                    <th>{{ Str::headline('kode') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('dari') }}</th>
                    <th>{{ Str::headline('project') }}</th>
                    <th>{{ Str::headline('referensi') }}</th>
                    <th>{{ Str::headline('nominal') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
</div>
