@extends('layouts.admin.layout.index')

@php
    $main = 'disposition';
    $title = 'disposisi aset';
@endphp

@section('title', Str::headline("edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        {{ Str::headline('asset') }}
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('edit ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    @can('view asset-disposition')
        <x-card-data-table title="{{ 'edit ' . $title }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="{{ route('admin.' . $main . '.update', ['disposition' => $model->id]) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="branch_id" id="branch_id" label="Branch" required autofocus>
                                <option value="{{ $model->branch->id }}" selected>{{ $model->branch->name }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <x-select name="asset_id" id="asset_id" label="Asset" required autofocus onchange="get_asset_detail($(this))">
                                <option value="{{ $model->asset->id }}" selected>{{ $model->asset->asset_name }}</option>
                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="last_book_value" name="last_book_value" label="nilai akhir aset" value="" readonly required class="commas-form text-end" value="{{ formatNumber($model->last_book_value) }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input class="datepicker-input" id="last_journal_date" name="last_journal_date" class="datepicker-input" label="tanggal terakhir depresiasi" readonly value="{{ localDate($model->last_journal_date) }}" />
                            </div>
                        </div>

                        <div class="col-md-3">
                            <x-select name="customer_id" id="customer_id" label="customer" required>
                                @if ($model->customer)
                                    <option value="{{ $model->customer->id }}" selected>{{ $model->customer->nama }}</option>
                                @endif
                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="date" name="date" label="tanggal" required class="datepicker-input" value="{{ localDate($model->date) }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="number" id="due" name="due" label="tempo" required onblur="calculate_due_date()" value="{{ $model->due }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="due_date" name="due_date" label="jatuh tempo" required readonly value="{{ localDate($model->due_date) }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-select name="bank_internal_id" id="bank_internal_id" label="bank" required>
                                @if ($model->bank_internal)
                                    <option value="{{ $model->bank_internal->id }}" selected>{{ $model->bank_internal->nama_bank }} - {{ $model->bank_internal->no_rekening }}</option>
                                @endif
                            </x-select>
                        </div>

                        <div class="col-md-12">
                            <hr>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-select name="gain_loss_coa_id" id="gain_loss_coa_id" label="akun laba rugi" required autofocus>
                                    <option value="{{ $model->gain_loss_coa->id }}" selected>{{ $model->gain_loss_coa->account_code }} - {{ $model->gain_loss_coa->name }}</option>
                                </x-select>
                            </div>
                            <div class="form-group">
                                <x-input type="text" id="location" name="location" label="lokasi" required value="{{ $model->location }}" />
                            </div>
                            <div class="form-group">
                                <label for="note" class="form-label">Note</label>
                                <textarea class="form-control" name="note" id="note" rows="5">{!! $model->note !!}</textarea>
                            </div>
                        </div>
                        <div class="col pt-40">
                            <div class="form-group">
                                <input type="checkbox" value="1" id="is_selling_asset" name="is_selling_asset" class="filled-in chk-col-primary" onclick="$('#form-selling-asset').toggleClass('d-none');show_selling_form($(this))" {{ $model->is_selling_asset == 1 ? 'checked' : '' }}>
                                <label for="is_selling_asset">Penjualan Asset</label>
                            </div>
                            <div id="form-selling-asset" class="{{ $model->is_selling_asset == 1 ? '' : 'd-none' }}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input type="text" id="selling_price" name="selling_price" label="harga jual" class="text-end commas-form" value="{{ formatNumber($model->selling_price) }}" />
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-select name="selling_coa_id" id="selling_coa_id" label="akun penjualan" autofocus>
                                                @if ($model->selling_coa)
                                                    <option value="{{ $model->selling_coa->id }}" selected>{{ $model->selling_coa->account_code }} - {{ $model->selling_coa->name }}</option>
                                                @endif
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-12"></div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-select name="tax_id" id="tax_id" label="pajak" autofocus>
                                                @if ($model->tax)
                                                    <option value="{{ $model->tax->id }}" selected>{{ $model->tax->name }}</option>
                                                @endif
                                            </x-select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <x-input type="text" id="tax_number" name="tax_number" label="faktur pajak" class="tax-reference-mask" value="{{ $model->tax_number }}" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.' . $main . '.index') }}" class="btn btn-secondary">Cancel</a>
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    @endcan
@endsection

@section('js')
    @can('edit asset-disposition')
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/admin/disposition/transaction.js') }}"></script>

        <script>
            sidebarMenuOpen('#finance-main-sidebar');
            sidebarMenuOpen('#asset-sidebar')
            sidebarActive('#disposition')

            initSelect2Search(`asset_id`, `{{ route('admin.select.asset') }}`, {
                id: "id",
                text: "asset_name"
            }, 0, {
                branch_id: function() {
                    return $('#branch_id').val();
                },
                status: 'active',
            });

            initSelect2SearchPagination(`gain_loss_coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            });

            initSelect2SearchPagination(`selling_coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            });

            initSelect2SearchPaginationData(`tax_id`, `{{ route('admin.select.tax') }}`, {
                id: "id",
                text: "name"
            });

            initSelect2SearchPaginationData(`customer_id`, `{{ route('admin.select.customer') }}`, {
                id: "id",
                text: "nama"
            });

            $('#customer_id').on('change', function() {
                $.ajax({
                    type: "get",
                    url: `${base_url}/customer/${$(this).val()}`,
                    success: function({
                        data
                    }) {
                        $('#due').val(data.top_days);
                        calculate_due_date();
                    },
                });

                let link = base_url + '/select/customer/customer-bank/' + $(this).val();
                initSelect2SearchPaginationData(`bank_internal_id`, link, {
                    id: "bank_internal_id",
                    text: "nama_bank,no_rekening"
                });
            });


            initMaskTaxReference()

            const calculate_due_date = () => {
                let due = $('#due').val();
                let date = convertLocalDate($('#date').val());

                let due_date = new Date(date);
                due_date.setDate(due_date.getDate() + parseInt(due));

                let day = due_date.getDate();
                let month = due_date.getMonth() + 1;
                let year = due_date.getFullYear();

                if (day < 10) {
                    day = '0' + day;
                }

                if (month < 10) {
                    month = '0' + month;
                }

                due_date = year + '-' + month + '-' + day;

                $('#due_date').val(localDate(due_date));
            }
        </script>

        @if (get_current_branch()->is_primary == 1)
            <script>
                initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                    id: "id",
                    text: "name"
                });
            </script>
        @endif
    @endcan
@endsection
