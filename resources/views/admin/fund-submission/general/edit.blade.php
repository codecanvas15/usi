@extends('layouts.admin.layout.index')

@php
    $main = 'fund-submission';
    $menu = 'pengajuan dana general';
@endphp

@section('title', Str::headline("edit $menu") . ' - ')

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
                        {{ Str::headline('edit ' . $menu) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main.update", ['fund_submission' => $model->id]) }}" method="post" enctype="multipart/form-data">
        <input type="hidden" name="_method" value="PUT">
        @csrf
        <x-card-data-table title="{{ 'edit ' . $menu }}">
            <x-slot name="header_content">
            </x-slot>
            <x-slot name="table_content">
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <h3 for="">No. <span>{{ $model->code }}</span></h3>
                                </div>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="branch_id" id="branch_id" label="branch" required>
                                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
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
                                    <x-input type="text" label="tanggal" name="date" id="date" class="datepicker-input" required value="{{ Carbon\Carbon::parse($model->date)->format('d-m-Y') }}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <x-select name="invoice_return_id" id="invoice_return_id" label="penerimaan retur">
                                    @if ($model->invoice_return)
                                        <option value="{{ $model->invoice_return_id }}">{{ $model->invoice_return->customer->nama }} - {{ $model->invoice_return->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <x-select name="cash_advance_receive_id" id="cash_advance_receive_id" label="pengembalian uang muka">
                                    @if ($model->cash_advance_receive)
                                        <option value="{{ $model->cash_advance_receive_id }}">{{ $model->cash_advance_receive->customer->nama }} - {{ $model->cash_advance_receive->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    @if ($model->invoice_return || $model->cash_advance_receive)
                                        <x-input type="text" label="kepada" name="to_name" id="to_name" required value="{!! $model->to_name !!}" readonly />
                                    @else
                                        <x-input type="text" label="kepada" name="to_name" id="to_name" required value="{!! $model->to_name !!}" />
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <x-select name="currency_id" id="currency_id" label="currency" required>
                                    <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-6">
                                @if ($model->currency->is_local)
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                                @else
                                    <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required />
                                @endif
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-select name="project_id" id="project_id" label="project">
                                        @if ($model->project)
                                            <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="referensi" id="referensi" value="{!! $model->reference !!}" />
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <x-input type="file" name="attachment" label="lampiran" id="attachment" accept=".jpg, .jpeg, .png, .pdf, .doc, .docx, .xls, .xlsx" helpers="jpg/jpeg/png/pdf/doc/docx/xls/xlsx | max. 5 MB" />
                                </div>
                                @if ($model->attachment)
                                    <a href="{{ asset('storage/' . $model->attachment) }}" target="_blank"><i class="fa fa-file" aria-hidden="true"></i> Lihat File Lampiran </a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-1"></div>
                    <div class="col-md-5">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <x-select name="coa_id" id="coa_id" required label="kas/bank" onchange="get_coa_detail($(this))">
                                        @if ($model->coa)
                                            <option value="{{ $model->coa_id }}">{{ $model->coa->account_code }} - {{ $model->coa->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                @if ($model->is_giro)
                                    <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" checked />
                                @else
                                    <x-input-checkbox label="bayar dengan giro" name="is_giro" id="is_giro" value="1" onclick="toggleGiroForm($(this))" />
                                @endif
                            </div>
                            <div class="col-md-12 {{ !$model->is_giro ? 'd-none' : '' }}" id="giro-form">
                                <div class="row">
                                    @include('admin.fund-submission.__giro_form', ['data' => $model->send_payment])
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-12 mt-4">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{{ Str::headline('akun') }} <span class="text-danger">*</span></th>
                                        <th>{{ Str::headline('keterangan') }}</th>
                                        <th class="text-end">{{ Str::headline('jumlah') }} <span class="text-danger">*</span></th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody id="outgoing-payment-detail-data">
                                    <input type="hidden" id="count_rows" value="{{ count($model->fund_submission_generals) }}">
                                    @foreach ($model->fund_submission_generals as $key => $fund_submission_general)
                                        <tr id="outgoing-payment-detail-{{ $key }}_edit" class="{{ $fund_submission_general->type }}_row">
                                            <td>
                                                <input type="hidden" name="is_return[]" value="{{ $fund_submission_general->invoice_return ? 'true' : '' }}" />
                                                <input type="hidden" name="type[]" value="{{ $fund_submission_general->type }}" />
                                                <input type="hidden" name="fund_submission_general_id[]" value="{{ $fund_submission_general->id }}" />
                                                <select name="coa_detail_id[]" id="coa_detail_id_{{ $key }}_edit" class="form-control" required autofocus style="width:100%">
                                                    <option value="{{ $fund_submission_general->coa_id }}">{{ $fund_submission_general->coa->name }}</option>
                                                </select>
                                            </td>
                                            <td>
                                                <input type="text" id="note_{{ $key }}_edit" name="note[]" class="form-control" placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $fund_submission_general->note }}" />
                                            </td>
                                            <td>
                                                <input type="text" id="debit_{{ $key }}_edit" name="debit[]" class="form-control commas-form text-end" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($fund_submission_general->debit) }}" />
                                            </td>
                                            <td>
                                                @if ($key != 0 || !in_array($fund_submission_general->type, ['return', 'cash_advance']))
                                                    <button type="button" class="btn btn-danger btn-sm" onclick="$('#outgoing-payment-detail-{{ $key }}_edit').remove();countTotal()"><i class="fa fa-times"></i></button>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="2">TOTAL</th>
                                        <th class="text-end" id="debit_total">{{ formatNumber($model->fund_submission_generals()->sum('debit')) }}</th>
                                        <th></th>
                                        <input type="hidden" id="debit_total_hide" value="{{ $model->fund_submission_generals()->sum('debit') }}">
                                    </tr>
                                    <tr>
                                        <th></th>
                                        <th class="text-end" colspan="2">
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
                        <x-button type="submit" color="primary" label="Save data" />
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
            initCoaSelect(`select[name="coa_detail_id[]"]`);

            initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
                id: "id",
                text: "kode,nama,negara"
            });

            initSelect2SearchPagination(`coa_id`, `{{ route('admin.select.coa') }}`, {
                id: "id",
                text: "account_code,name"
            }, 0, {
                account_type: "Cash & Bank",
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
