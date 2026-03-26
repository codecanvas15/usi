<div class="tab-pane {{ $is_active ? 'active' : '' }}" id="{{ $tab }}" role="tabpanel">
    <x-card-data-table title="{{ $title }}">
        <x-slot name="header_content">
            <div class="row mb-4">
                @if (get_current_branch()->is_primary)
                    <div class="col-md-2">
                        <div class="form-group">
                            <x-select id="branch-select-deposite" label="branch">

                            </x-select>
                        </div>
                    </div>
                @endif
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="from_date_deposite" name="from_date" label="tanggal awal" value="{{ Carbon\Carbon::now()->startOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <x-input class="datepicker-input" id="to_date_deposite" name="to" label="tanggal akhir" value="{{ Carbon\Carbon::now()->endOfMonth()->format('d-m-Y') }}" required />
                    </div>
                </div>
                <div class="col-md-3 row align-self-end">
                    <div class="form-group">
                        <x-button type="button" color="primary" id="set-deposite-table" icon="search" fontawesome onclick="$('#deposite-table').DataTable().ajax.reload()" />
                        <x-button color="info" icon="plus" label="Tambah" link="{{ route('admin.cash-advance-payment.create') }}" />
                        @can('create ' . $permission)
                        @endcan
                    </div>
                </div>
            </div>
        </x-slot>
        <x-slot name="table_content">
            @include('components.validate-error')

            <x-table id="deposite-table">
                <x-slot name="table_head">
                    <th></th>
                    <th>#</th>
                    <th>{{ Str::headline('kode') }}</th>
                    <th>{{ Str::headline('tanggal') }}</th>
                    <th>{{ Str::headline('kepada') }}</th>
                    <th>{{ Str::headline('') }}</th>
                    <th>{{ Str::headline('jumlah') }}</th>
                    <th>{{ Str::headline('project') }}</th>
                    <th>{{ Str::headline('referensi') }}</th>
                    <th>{{ Str::headline('status') }}</th>
                    <th>{{ Str::headline('action') }}</th>
                </x-slot>
                <x-slot name="table_body">

                </x-slot>
            </x-table>
        </x-slot>

    </x-card-data-table>
</div>
