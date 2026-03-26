@extends('layouts.admin.layout.index')

@php
    $main = 'cash-advance-payment';
    $menu = 'pengeluaran dana';
@endphp

@section('title', Str::headline("Edit $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.outgoing-payment.index') }}?tab=deposite">{{ Str::headline($main) }}</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('Edit ' . $main) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <form id="form-data" action="{{ route("admin.$main.update", ['cash_advance_payment' => $model->id]) }}" method="post">
        @method('PUT')
        @csrf
        <x-card-data-table title="{{ 'Edit ' . $main }}">
            <x-slot name="table_content">
                @include('components.validate-error')
                <form action="" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <h3 for="">No. <span>{{ $model->bankCodeMutation }}</span></h3>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="date" name="date" label="Tanggal" required value="{{ localDate($model->date) }}" class="datepicker-input" />
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <x-input type="text" id="sequence_code" name="sequence_code" label="nomor bukti" value="{{ $model->bank_code_mutation }}" readonly />
                            </div>
                        </div>
                        <div class="col-md-3" id="form-fund-submission">
                            <div class="form-group">
                                <x-select name="fund_submission_id" id="fund_submission_id" label="pengajuan dana" onchange="getFundSubmission($(this))">
                                    @if ($model->fund_submission)
                                        <option value="{{ $model->fund_submission_id }}">{{ $model->fund_submission->code }}</option>
                                    @endif
                                </x-select>
                            </div>
                        </div>
                    </div>
                    <div class="row" id="form_detail">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="branch_id" id="branch_id" label="branch" required disabled>
                                        <option value="{{ $model->branch_id }}" selected>{{ $model->branch->name }}</option>
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" label="jenis pengajuan" name="item" id="item" required value="DP" readonly />
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <x-select name="to_model" id="to_model" label="Bayar Ke" required disabled>
                                    <option @if ($model->to_model == 'App\Models\Employee') selected @endif value="App\Models\Employee">Karyawan</option>
                                    <option @if ($model->to_model == 'App\Models\Vendor') selected @endif value="App\Models\Vendor">Vendor</option>
                                </x-select>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="to_id" class="form-label">Vendor/Supplier</label>
                                    <select name="to_id" id="to_id" class="form-control">
                                        <option value="{{ $model->model_reference->id }}">{{ $model->model_reference->name ?? $model->model_reference->nama }}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <x-select name="currencies" id="currencies" label="currency" required disabled>
                                    <option value="{{ $model->currency->id }}" selected>{{ $model->currency->nama }}</option>
                                </x-select>
                            </div>
                            <div class="col-md-3">
                                <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="{{ formatNumber($model->exchange_rate) }}" required readonly />
                            </div>
                            <div class="col-md-12"></div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-select name="project_id" id="project_id" label="project" disabled>
                                        @if ($model->project)
                                            <option value="{{ $model->project_id }}">{{ $model->project->name }}</option>
                                        @endif
                                    </x-select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <x-input type="text" label="note" name="referensi" id="referensi" value="{!! $model->reference !!}" readonly />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 mt-4">
                                <table class="table table-striped">
                                    <thead>
                                        <tr id="cash-advance-detail-0">
                                            <input type="hidden" name="type[]" value="cash_bank">
                                            <input type="hidden" name="position[]" value="credit">
                                            <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_bank->id }}">
                                            <td>
                                                <div class="form-group">
                                                    <label for="coa_detail_id_0" class="form-label">Akun Kas/Bank</label><br>
                                                    <select name="coa_detail_id[]" id="coa_detail_id_0" class="form-control cash_bank_coa_id" required autofocus style="width:100%">
                                                        <option value="{{ $model->cash_advance_cash_bank->coa_id }}">{{ $model->cash_advance_cash_bank->coa->account_code }} - {{ $model->cash_advance_cash_bank->coa->name }}</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label for="note_0" class="form-label">Keterangan</label>
                                                    <input type="text" id="note_0" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_bank->note }}" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label for="note_0" class="form-label">Jumlah (Kredit)</label>
                                                    <input type="text" id="amount_0" name="amount[]" class="form-control commas-form text-end cash_bank_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_bank->credit) }}" readonly />
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr id="cash-advance-detail-1">
                                            <input type="hidden" name="type[]" value="cash_advance">
                                            <input type="hidden" name="position[]" value="debit">
                                            <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $model->cash_advance_cash_advance->id }}">
                                            <td>
                                                <div class="form-group">
                                                    <label for="coa_detail_id_1" class="form-label">Akun Uang Muka</label><br>
                                                    <select name="coa_detail_id[]" id="coa_detail_id_1" class="form-control cash_advance_coa_id" required autofocus style="width:100%">
                                                        <option value="{{ $model->cash_advance_cash_advance->coa_id }}">{{ $model->cash_advance_cash_advance->coa->account_code }} - {{ $model->cash_advance_cash_advance->coa->name }}</option>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label for="note_1" class="form-label">Keterangan</label>
                                                    <input type="text" id="note_1" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $model->cash_advance_cash_advance->note }}" readonly />
                                                </div>
                                            </td>
                                            <td>
                                                <div class="form-group">
                                                    <label for="note_1" class="form-label">Jumlah</label>
                                                    <input type="text" id="amount_1" name="amount[]" class="form-control commas-form text-end cash_advance_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($model->cash_advance_cash_advance->debit) }}" readonly />
                                                </div>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr class="bg-info">
                                            <th colspan="4">{{ Str::headline('biaya lain - lain') }}</th>
                                        </tr>
                                        <tr>
                                            <th>{{ Str::headline('akun') }}</th>
                                            <th>{{ Str::headline('keterangan') }}</th>
                                            <th class="text-end">{{ Str::headline('jumlah') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody id="cash-advance-detail-data">
                                        <input type="hidden" id="count_rows" value="{{ count($model->cash_advance_payment_details) }}">
                                        @forelse ($model->cash_advance_others as $key => $item)
                                            <tr id="cash-advance-detail-{{ $key + 2 }}">
                                                <input type="hidden" name="type[]" value="other">
                                                <input type="hidden" name="position[]" value="debit">
                                                <input type="hidden" name="fund_submission_cash_advance_id[]" value="{{ $item->id }}">
                                                <td>
                                                    <select name="coa_detail_id[]" id="coa_detail_id_{{ $key + 2 }}" class="form-control other_coa_id" required autofocus style="width:100%">
                                                        <option value="{{ $item->coa_id }}">{{ $item->coa->account_code }} - {{ $item->coa->name }}</option>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" id="note_{{ $key + 2 }}" name="note[]" class="form-control" required placeholder="{{ Str::headline('masukkan keterangan') }}" value="{{ $item->note }}" readonly />
                                                </td>
                                                <td>
                                                    <input type="text" id="amount_{{ $key + 2 }}" name="amount[]" class="form-control commas-form text-end other_amount" required placeholder="{{ Str::headline('masukkan nominal') }}" onkeyup="countTotal()" value="{{ formatNumber($item->debit) }}" readonly />
                                                </td>
                                                <td>

                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center">Tidak Ada Data</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="2"></th>
                                            <th></th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-end">
                            <a href="{{ route('admin.outgoing-payment.index') }}?tab=deposite" class="btn btn-secondary">Cancel</a>
                            @if ($model->status != 'approve')
                                <x-button type="submit" color="primary" label="Save data" />
                            @endif
                        </div>
                    </div>
                </form>
            </x-slot>
        </x-card-data-table>
    </form>
@endsection

@section('js')
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/admin/cash-advance-payment/index.js') }}"></script>
    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#outgoing-payment-sidebar');
        sidebarActive('#outgoing-payment');
    </script>
@endsection
