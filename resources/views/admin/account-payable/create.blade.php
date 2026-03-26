@extends('layouts.admin.layout.index')

@php
    $main = 'account-payable';
    $menu = 'pembayaran supplier';
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
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=account-payable">{{ Str::headline($main) }}</a>
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
    <form action="{{ route("admin.$main.store") }}" method="post">
        @csrf
        <x-card-data-table title="{{ 'tambah ' . $menu }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-3">
                            <x-select name="vendor_id" id="vendor_id" label="Pilih Vendor" required autofocus>
                            </x-select>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" label="tanggal bayar" name="date" id="date" class="datepicker-input" required value="{{ date('d-m-Y') }}" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <x-select name="fund_submission_id" id="fund_submission_id" label="Pilih Pengajuan Dana" required autofocus onchange="getFundSubmission($(this))">
                            </x-select>
                            <input type="hidden" id="local_currency_id" value="{{ get_local_currency()->id }}">
                            <input type="hidden" id="local_currency_symbol" value="{{ get_local_currency()->simbol }}">
                        </div>
                        <div class="col-md-12">
                            <hr>
                        </div>
                    </div>
                    <div id="data-detail" class="d-none">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Branch</label>
                                            <p type="text" id="branch_name"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Tanggal Pengajuan Dana</label>
                                            <p type="text" id="fund_submission_date"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Currency</label>
                                            <p type="text" id="currency_name"></p>
                                            <input type="hidden" id="currency_id">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Purchase Invoice Currency</label>
                                            <p type="text" id="supplier_invoice_currency"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kurs</label>
                                            <x-input id="exchange_rate" name="exchange_rate" class="commas-form text-end" onkeyup="countExchangeRateGap()" />
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Note</label>
                                            <p type="text" id="reference"></p>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Project</label>
                                            <p type="text" id="project"></p>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Kas/Bank</label>
                                            <p type="text" id="coa_id"></p>
                                            <input type="hidden" name="coa_id" id="selected_coa_id">
                                            <x-button type="button" color="info" icon="pen-to-square" label="edit" size="sm" dataToggle="modal" dataTarget="#edit-bank-modal" />
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="" />
                                            <small for="" class="text-danger">jika nomor bukti kosong, akan diisi kode otomatis</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="form-label">Note</label>
                                            <p type="text" id="parent_note"></p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="col-md-6" id="giro-information">

                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <h3>Purchase Invoice</h3>
                                    <div id="selected_supplier_invoice_table">

                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <h3>Lain - Lain</h3>
                                    <div class="table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-info">
                                                <tr>
                                                    <th>{{ Str::headline('akun') }}</th>
                                                    <th>{{ Str::headline('keterangan') }}</th>
                                                    <th class="text-end">{{ Str::headline('jumlah') }} <span class="currency_id_code"></span></th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            <tbody id="fund_submission_supplier_other_data">

                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <th>TOTAL</th>
                                                    <th></th>
                                                    <th class="text-end" id="debit_total">0</th>
                                                    <input type="hidden" id="debit_total_hide">
                                                    <th></th>
                                                </tr>
                                                <tr>
                                                    <th class="text-end" colspan="3">
                                                        <x-button color="success" label="Tambah Baris +" type="button" onclick="addAccountPayableOtherRow()" class="btn-sm" />
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="change_bank_reason" id="change-bank-reason-input">
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <x-button type="reset" color="secondary" label="cancel" link="{{ route('admin.outgoing-payment.index') }}?tab=account-payable" />
                            <x-button type="submit" color="primary" label="Save data" />
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>

    <x-modal title="edit bank" id="edit-bank-modal" headerColor="info">
        <x-slot name="modal_body">
            <div class="row">
                <div class="col-md-12">
                    <x-select name="coa_id_bank_select" required id="coa-bank-edit-select" required label="kas/bank">

                    </x-select>
                </div>
                <div class="col-md-12">
                    <x-input name="bank_change_reason" id="change-bank-reason" label="alasan ganti bank" required></x-input>
                </div>
            </div>

            <x-slot name="modal_footer">
                <x-button type="button" color="secondary" dataDismiss="modal" label="cancel" dataDismiss="modal" />
                <x-button type="button" color="primary" label="Save data" id="save-edit-bank-modal" />
            </x-slot>
        </x-slot>
    </x-modal>

@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/select/coa.js') }}"></script>
    <script src="{{ asset('js/admin/account-payable/transaction.js') }}?v=100"></script>

    <script>
        checkClosingPeriod($('#date'))
        $('body').addClass('sidebar-collapse');
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');

        $('#date').on('change', function(e) {
            checkFundSubmissionDate($(this))
            checkClosingPeriod($(this))
        })

        initSelect2Search('vendor_id', `${base_url}/select/vendor`, {
            id: "id",
            text: "nama"
        });

        initSelect2Search('fund_submission_id', `${base_url}/select/fund-submission`, {
            id: "id",
            text: "code,to_name,total"
        }, 0, {
            item: "lpb",
            available: true,
            to_id: function() {
                return $('#vendor_id').val();
            },
            date: function() {
                return $('#date').val();
            }
        });

        $('#sequence_code').on('blur', function() {
            check_bank_code(
                '#selected_coa_id',
                '#sequence_code',
                '#date',
                'out'
            );
        });
    </script>
@endsection
