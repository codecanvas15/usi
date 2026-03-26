@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $menu = 'pengajuan dana general';
@endphp

@section('title', Str::headline("tambah $menu") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
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
    <form id="form-data" action="{{ route("admin.$main.store") }}" method="post" enctype="multipart/form-data">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $menu }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                <div id="errorRl" class="alert alert-danger" role="alert" style="display: none">
                    <span id="errorRlMessage"></span>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="branch_id" id="branch_id" label="branch" required>
                                        <option value="{{ get_current_branch()->id }}" selected>
                                            {{ get_current_branch()->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="jenis pengajuan" name="item" id="item" required value="General" readonly />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ date('d-m-Y') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <x-select name="invoice_return_id" id="invoice_return_id" label="pembayaran retur">
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <x-select name="cash_advance_receive_id" id="cash_advance_receive_id" label="pengembalian uang muka">
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="kepada" name="to_name" id="to_name" required value="" />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                    <option value="{{ get_local_currency()->id }}" selected>
                                        {{ get_local_currency()->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="project_id" id="project_id" label="project">

                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="referensi" id="referensi" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="file" name="attachment" label="lampiran" id="attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-select name="coa_id" id="coa_id" required label="kas/bank" onchange="get_coa_detail($(this))">
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-12 giro-checkbox d-none">
                                <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                            </div>
                            <div class="col-md-12 d-none" id="giro-form">
                                <div class="row">
                                    @include('admin.fund-submission.__giro_form')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ Str::headline('akun') }} <span class="text-danger">*</span></th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }} <span class="text-danger">*</span>
                                        </th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="outgoing-payment-detail-data">
                                    <input type="hidden" id="count_rows" value="0">
                                    <tr id="outgoing-payment-detail-0">
                                        <td>
                                            <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control" required autofocus style="width:100%">
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" id="note_0" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" />
                                        </td>
                                        <td>
                                            <input type="text" id="debit_0" name="debit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" />
                                        </td>
                                        <td></td>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="debit_total">0</th>
                                        <input type="hidden" id="debit_total_hide">
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <th class="text-end" colspan="3">
                                            <x-button color="success" label="Tambah Baris +" type="button" onclick="addNewOutgoingPaymentDetailRow()" class="btn-sm" />
                                        </th>
                                        <th></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="box-footer">
                    <div class="float-end">
                        <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                        <x-button id="save-data" type="submit" color="primary" label="Save data" />
                    </div>
                </div>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection
@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/fund-submission/general.js') }}?v=100"></script>
    <script>
        $(document).ready(function() {
            initSelect2SearchPagination(`coa_detail_id_0`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            });

            initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: "Cash & Bank",
            });

            initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,nama,negara"
            });

            $('#currency_id').change(function(e) {
                e.preventDefault();
                // $('#coa_id').val(null).trigger('change');
                $.ajax({
                    type: "get",
                    url: `{{ route('admin.currency.detail') }}/${this.value}`,
                    success: function({
                        data
                    }) {
                        if (data.is_local) {
                            $('#exchange_rate').val(1);
                            $('#exchange_rate').attr('readonly', 'readonly');
                        } else {
                            $('#exchange_rate').removeAttr('readonly');
                            $('#exchange_rate').attr('readonly', false);
                        }
                    }
                });
            });

            initSelect2Search(`project_id`, `{{ route('admin.select.project') }}`, {
                id: "id",
                text: "code,name"
            }, 2, {
                branch_id: function() {
                    return $('#branch_id').val();
                }
            });

            $('#form-data').submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "post",
                    url: `${base_url}/rate-limiter/ajax`,
                    data: {
                        _token: token,
                        key: "create: " + "{{ $main }}, " + $('#item').val(),
                        attempts: 2, // default is 2 attempts
                        decay_seconds: 3,
                    },
                    success: function(response) {
                        if (response.is_too_many_requests == true) {
                            let waitingTime = parseInt(response.available_at_time);

                            $('#errorRl').show();
                            $('#errorRlMessage').text(
                                'Terlalu banyak permintaan menyimpan data, harap tunggu ' +
                                waitingTime + " detik lagi");

                            let showError = setInterval(() => {
                                waitingTime--;

                                if (waitingTime > 0 && waitingTime <= 60) {
                                    $('#errorRlMessage').text(
                                        'Terlalu banyak permintaan menyimpan data, harap tunggu ' +
                                        waitingTime + " detik lagi");
                                }

                                if (waitingTime == 0) {
                                    $('#errorRl').hide();
                                    $('#save-data').prop('disabled', false);
                                    clearInterval(showError);
                                }
                            }, 1000);
                        } else {
                            $('#form-data').unbind('submit').submit();
                        }
                    }
                });
            });
        })
    </script>
    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#fund-submission');
    </script>
@endsection
