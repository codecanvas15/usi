@extends('layouts.admin.layout.index')

@php
    $main = 'payroll';
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
                    <li class="breadcrumb-item active">
                        <a href="{{ route("admin.$main.index") }}">{{ Str::headline($main) }}</a>
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
    @can("edit $main")
        <form action="{{ route("admin.$main.update", $salary->id) }}" method="post" enctype="multipart/form-data" id="form-data">
            @csrf
            @method('PUT')
            <x-card-data-table title="{{ 'edit ' . $main }}">
                <x-slot name="header_content">

                </x-slot>
                <x-slot name="table_content">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="">Periode</label>
                                <select name="period" id="period" class="form-control select2" onchange="getPeriodDetail(this)">
                                    <option value="" selected>-- pilih periode --</option>
                                    @foreach ($periods as $period)
                                        @php
                                            $periodStartDate = \Carbon\Carbon::parse($period->date)->translatedFormat('d F Y');
                                            $periodEndDate = \Carbon\Carbon::parse($period->end_date)->translatedFormat('d F Y');

                                            if ($periodStartDate == $periodEndDate) {
                                                $periodDate = $periodStartDate;
                                            } else {
                                                $periodDate = $periodStartDate . ' - ' . $periodEndDate;
                                            }
                                        @endphp
                                        <option value="{{ $period->id }}" @if ($period->id == $salary->payroll_period_id || old('period') == $period->id) selected @endif>[{{ ucwords($period->type) }}] {{ $periodDate }}</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="period_type" id="periodType" value="{{ $salary->payrollPeriod->type }}">
                                <small class="text-danger error_period" style="display: none">Periode belum dipilih.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Pegawai</label>
                                <select class="form-control select2" id="user" name="user">
                                    <option value="" selected>-- pilih pegawai --</option>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}" @if ($user->id == $auth->id || old('user') == $user->id) selected @endif>{{ ucwords($user->name) }}</option>
                                    @endforeach
                                </select>
                                <small class="text-danger error_user" style="display: none">Pegawai
                                    belum dipilih.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status Pernikahan</label>
                                <p id="non_taxable_income">{{ $salary->user->non_taxable_income->note }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status NPWP</label>
                                <p id="npwp">{{ $salary->user->npwp ?? 'Tidak ada NPWP' }}</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Gaji Take Home Pay </label>
                                <input class="form-control commas-form" id="baseSalary" name="base_salary" @if (old('base_salary')) value="{{ old('base_salary') }}"
                            @else
                                value="{{ formatNumber($salary->base_salary) }}" @endif onkeyup="handleOnChangeAmountInput()" placeholder="Masukkan gaji take home pay..." onblur="calculateIncomeTax();">
                                <small class="text-danger error_base_salary" style="display: none">Gaji
                                    THP belum diisi.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah Hari Kerja</label>
                                <input class="form-control" id="workDaysTotal" name="work_days_total" @if (old('work_days_total')) value="{{ old('work_days_total') }}"
                            @else
                                value="{{ $salary->work_days_total }}" @endif onkeyup="handleOnChangeAmountInput()" placeholder="Masukkan jumlah hari kerja...">
                                <small class="text-danger error_work_days_total" style="display: none">Jumlah hari kerja belum diisi.</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Jumlah Masuk Kerja</label>
                                <input class="form-control" id="workDays" name="work_days" @if (old('work_days')) value="{{ old('work_days') }}"
                            @else
                                value="{{ $salary->work_days }}" @endif placeholder="Masukkan jumlah masuk kerja..." onkeyup="handleOnChangeAmountInput()">
                                <small class="text-danger error_work_days" style="display: none">Jumlah
                                    masuk kerja belum diisi.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row mb-10">
                        <div class="col">
                            <div class="form-group">
                                <label>Jumlah Alpha</label>
                                <input class="form-control" id="alphaDays" name="alpha_days" value="{{ $salary->alpha_days }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            @if ($salary->is_absence_calculated)
                                <x-input-checkbox name="is_absence_calculated" id="is_absence_calculated" label="Hitung Alpha" value="1" onclick="sumSalary()" checked />
                            @else
                                <x-input-checkbox name="is_absence_calculated" id="is_absence_calculated" label="Hitung Alpha" value="1" onclick="sumSalary()" />
                            @endif
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Jumlah Izin</label>
                                <input class="form-control" id="absenceDays" name="absences_days" value="{{ $salary->absences_days }}" readonly>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label>Jumlah Cuti</label>
                                <input class="form-control" id="leaveDays" name="leave_days" value="{{ $salary->leave_days }}" readonly>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-card-data-table>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ Str::headline('Cuti') }}</h5>
                </div>
                <div class="box-body">
                    <p id="emptyCuti" class="mb-0" style="display: block">Belum ada data.</p>
                    <div class="accordion" id="accordionCuti"></div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ Str::headline('Izin') }}</h5>
                </div>
                <div class="box-body">
                    <p id="emptyIzin" class="mb-0" style="display: block">Belum ada data.</p>
                    <div class="accordion" id="accordionIzin"></div>
                </div>
            </div>
            <div class="box">
                <div class="box-header">
                    <h3 class="box-title">{{ Str::headline('Tunjangan & Potongan') }}</h5>
                </div>
                <div class="box-body">
                    <div class="row border-bottom border-primary mb-20">
                        <h4 class="mb-20">TUNJANGAN</h4>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Nama Tunjangan</label>
                                <x-select id="allowanceId" class="select2" onchange="getSalaryItemDetail(this, 'allowance')">
                                </x-select>
                                <input type="hidden" id="allowanceName" class="form-control" value="" placeholder="Nama tunjangan">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="">Persentase</label>
                                <input class="form-control commas-form text-end" id="allowancePercentage" name="allowance_percentage" value="0" onkeyup="sumAllowance()">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Besaran Tunjangan</label>
                                <input class="form-control commas-form text-end" id="allowanceAmount" name="allowance_amount" value="0" onkeyup="sumAllowance()">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="">Jumlah</label>
                                <input class="form-control text-end" id="allowanceQty" name="allowance_qty" value="0" onkeyup="sumAllowance()">
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <label for="">Total</label>
                                <input class="form-control commas-form text-end" id="allowanceTotal" name="allowance_total" value="0" readonly>
                            </div>
                        </div>
                        <div class="col-sm-2 d-flex align-items-end">
                            <div class="form-group ">
                                <button type="button" class="btn btn-primary btn-plus" onclick="addAllowance()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div id="allowanceDetail" class="col-sm-12 mb-10">
                        </div>
                    </div>
                    <div class="row mb-20">
                        <h4 class="mb-20">POTONGAN</h4>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Nama Potongan</label>
                                <x-select id="deductionId" class="select2" onchange="getSalaryItemDetail(this, 'deduction')">
                                </x-select>
                                <input type="hidden" class="form-control" id="deductionName" value="" placeholder="Nama potongan">
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <label for="">Persentase</label>
                                <input class="form-control commas-form text-end" id="deductionPercentage" name="deduction_percentage" value="0" onkeyup="sumDeduction()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="">Besaran Potongan</label>
                                <input class="form-control commas-form text-end" id="deductionAmount" name="deduction_amount" value="0" onkeyup="sumDeduction()">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <div class="form-group">
                                <label for="">Jumlah</label>
                                <input class="form-control text-end" id="deductionQty" name="deduction_qty" value="0" onkeyup="sumDeduction()">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label for="">Total</label>
                            <div class="form-group">
                                <input class="form-control commas-form text-end" id="deductionTotal" name="deduction_total" value="0" readonly>
                            </div>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <div class="form-group">
                                <button type="button" class="btn btn-primary btn-plus" onclick="addDeduction()">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div id="deductionDetail" class="col-sm-12 mb-10"></div>
                    </div>
                    <div class="row border-top border-danger py-20">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gaji Kotor</label>
                                <input class="form-control commas-form" id="bruttoSalary" name="brutto_salary" value="0" readonly>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Gaji Bersih</label>
                                <input class="form-control commas-form" id="nettoSalary" name="netto_salary" value="0" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="box-footer">
                <div class="d-flex justify-content-end gap-3">
                    <x-button type="reset" color="secondary" label="cancel" link="{{ url()->previous() }}" />
                    <x-button type="submit" color="primary" label="Save data" class="btn-save" />
                </div>
            </div>
        </form>
    @endcan
@endsection

@section('js')
    @can("edit $main")
        <script src="{{ asset('js/helpers/helpers.js') }}"></script>
        <script src="{{ asset('js/form/select2search.js') }}"></script>
        <script>
            let cutiId = 0;
            let izinId = 0;

            let allowanceCount = 0;
            let deductionCount = 0;
            let feeCount = 0;
            let cuti_amount = 0;
            let izin_amount = 0;

            initCommasForm();

            $('.btn-save').click(function(e) {
                e.preventDefault();
                submit();
            });

            const initPeriod = () => {
                let periodId = "{{ $salary->payroll_period_id }}";
                $('#period').val(periodId).trigger('change');
            }

            const initFees = () => {
                let fees = @json($salary->feeSalaries);
                fees.forEach(fee => {
                    let type = fee.type;
                    let can_delete = '';

                    $('#feeDetail').append(`
                <div id="fee${feeCount}" class="row ${type} mb-1">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="hidden" name="fee_detail_type[]" value="${type}">
                            <input type="text" class="form-control" name="fee_detail_name[]" value="${ucfirst(fee.name)}" readonly>
                        </div>
                    </div>
                      <div class="col-sm-1">
                        <div class="form-group">
                            <input type="text" class="form-control fee-percentage${feeCount} text-end" name="fee_detail_percentage[]" value="${formatRupiahWithDecimal(fee.percentage)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="text" class="form-control commas-form fee-amount${feeCount} text-end" name="fee_detail_amount[]" value="${formatRupiahWithDecimal(fee.amount)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <input type="text" class="form-control fee-qty${feeCount} text-end" name="fee_detail_qty[]" value="${fee.qty}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="text" class="form-control commas-form fee-total${feeCount} text-end" name="fee_detail_total[]" value="${formatRupiahWithDecimal(fee.total)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2 ${can_delete}">
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger" onclick="deleteFee(${feeCount})">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);

                    initCommasForm();
                    sumSalary();

                    feeCount++;
                });
            }

            const initAllowances = () => {
                let allowances = @json($salary->allowanceSalaries);
                allowances.forEach(allowance => {
                    let type = allowance.type;
                    let can_delete = '';
                    if (['income-tax', 'base-salary', 'day-off'].includes(type)) {
                        can_delete = 'd-none';
                    }

                    $('#allowanceDetail').append(`
                <div id="allowance${allowanceCount}" class="row ${type} mb-1">
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="hidden" name="allowance_detail_type[]" value="${type}">
                            <input type="text" class="form-control" name="allowance_detail_name[]" value="${ucfirst(allowance.name)}" readonly>
                        </div>
                    </div>
                      <div class="col-sm-1">
                        <div class="form-group">
                            <input type="text" class="form-control allowance-percentage${allowanceCount} text-end" name="allowance_detail_percentage[]" value="${formatRupiahWithDecimal(allowance.percentage)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="text" class="form-control commas-form allowance-amount${allowanceCount} text-end" name="allowance_detail_amount[]" value="${formatRupiahWithDecimal(allowance.amount)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-1">
                        <div class="form-group">
                            <input type="text" class="form-control allowance-qty${allowanceCount} text-end" name="allowance_detail_qty[]" value="${allowance.qty}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2">
                        <div class="form-group">
                            <input type="text" class="form-control commas-form allowance-total${allowanceCount} text-end" name="allowance_detail_total[]" value="${formatRupiahWithDecimal(allowance.total)}" readonly>
                        </div>
                    </div>
                    <div class="col-sm-2 ${can_delete}">
                        <div class="btn-group">
                            <button type="button" class="btn btn-danger" onclick="deleteAllowance(${allowanceCount})">
                                <i class="fa fa-minus"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `);

                    initCommasForm();
                    sumSalary();

                    allowanceCount++;
                });
            }

            const initDeductions = () => {
                let deductions = @json($salary->deductionSalaries);
                deductions.forEach(deduction => {
                    let type = deduction.type;
                    let can_delete = '';
                    if (['income-tax', 'base-salary', 'day-off'].includes(type)) {
                        can_delete = 'd-none';
                    }

                    if (deduction.name !== 'Alpha') {
                        $('#deductionDetail').append(`
                    <div id="deduction${deductionCount}" class="row ${type} mb-1">
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="hidden" name="deduction_detail_type[]" value="${type}">
                                <input type="text" class="form-control" name="deduction_detail_name[]" value="${ucfirst(deduction.name)}" readonly>
                            </div>
                        </div>
                         <div class="col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control deduction-percentage${deductionCount} text-end" name="deduction_detail_percentage[]" value="${formatRupiahWithDecimal(deduction.percentage)}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" class="form-control commas-form deduction-amount${deductionCount} text-end" name="deduction_detail_amount[]" value="${formatRupiahWithDecimal(deduction.amount)}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-1">
                            <div class="form-group">
                                <input type="text" class="form-control deduction-qty${deductionCount} text-end" name="deduction_detail_qty[]" value="${deduction.qty}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-2">
                            <div class="form-group">
                                <input type="text" class="form-control commas-form deduction-total${deductionCount} text-end" name="deduction_detail_total[]" value="${formatRupiahWithDecimal(deduction.total)}" readonly>
                            </div>
                        </div>
                        <div class="col-sm-2 ${can_delete}">
                            <div class="btn-group">
                                <button type="button" class="btn btn-danger" onclick="deleteDeduction(${deductionCount})">
                                    <i class="fa fa-minus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                `);

                        initCommasForm();
                        sumSalary();

                        deductionCount++;
                    }
                });
            }

            const init = () => {
                initPeriod();
                initFees();
                initAllowances();
                initDeductions();
                handleOnChangeEmployeeAndPeriod();
            }
            init();

            initCommasForm();

            function getCuti() {
                let employeeId = $('#user').val();
                let payrollPeriod = $('#period').val();

                $('#accordionCuti').html('');

                if (employeeId !== '' && payrollPeriod !== 0) {
                    $.ajax({
                        url: "{{ route('admin.payroll.get-cuti') }}",
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            _token: token,
                            employee_id: employeeId,
                            period_id: payrollPeriod,
                        },
                        success: function(data) {
                            cuti_amount = 0;

                            if (data.length > 0) {
                                $('#emptyCuti').hide();

                                $.each(data, function(index, value) {
                                    let note = value.note ?? 'Cuti';

                                    cuti_amount += value.day;

                                    let date = '';
                                    let html_date = ``;

                                    if (value.from_date == value.to_date) {
                                        date = value.from_date
                                        html_date = `
                                            <tr>
                                                <th>{{ Str::headline('Tanggal') }}</th>
                                                <td>${date}</td>
                                            </tr>
                                        `;
                                    } else {
                                        date = value.from_date + ' - ' + value.to_date;
                                        html_date = `
                                            <tr>
                                                <th>{{ Str::headline('Tanggal') }}</th>
                                                <td>${date}</td>
                                            </tr>
                                        `;
                                    }

                                    $('#accordionCuti').append(`
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="cuti-${cutiId}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cuti${cutiId}" aria-expanded="false" aria-controls="cuti${cutiId}">
                                                    ${date}
                                                </button>
                                            </h2>
                                            <div id="cuti${cutiId}" class="accordion-collapse collapse" aria-labelledby="cuti-${cutiId}" data-bs-parent="#accordionCuti">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-striped" id="cuti-table">
                                                                <thead class="bg-dark">
                                                                    <th></th>
                                                                    <th></th>
                                                                </thead>
                                                                <tbody id="izin-table-body">
                                                                    <tr>
                                                                        <th>{{ Str::headline('Nama Pegawai') }}</th>
                                                                        <td>${value.employee.name}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ Str::headline('NIK') }}</th>
                                                                        <td>${value.employee.NIK}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ Str::headline('Departemen') }}</th>
                                                                        <td>${value.employee.division.name ?? '-'}</td>
                                                                    </tr>
                                                                    ${html_date}
                                                                    <tr>
                                                                        <th>{{ Str::headline('Keterangan') }}</th>
                                                                        <td>${note}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                    cutiId++;
                                });
                                $('#leaveDays').val(cuti_amount);
                            } else {
                                $('#emptyCuti').show();
                                $('#leaveDays').val(0);
                            }
                            handleOnChangeAmountInput();
                        }
                    });
                }
            }

            function getIzin() {
                let employeeId = $('#user').val();
                let payrollPeriod = $('#period').val();

                $('#accordionIzin').html('');

                if (employeeId !== '' && payrollPeriod !== 0) {
                    $.ajax({
                        url: "{{ route('admin.payroll.get-izin') }}",
                        method: 'POST',
                        dataType: 'JSON',
                        data: {
                            _token: token,
                            employee_id: employeeId,
                            period_id: payrollPeriod,
                        },
                        success: function(data) {
                            izin_amount = 0;

                            if (data.length > 0) {
                                $('#emptyIzin').hide();

                                $.each(data, function(index, value) {
                                    let note = value.note ?? '-';

                                    izin_amount += value.days;

                                    let html_date = ``;
                                    if (value.letter_date_start == value.letter_date_end) {
                                        html_date = `
                                            <tr>
                                                <th>{{ Str::headline('Tanggal') }}</th>
                                                <td>${value.letter_date_start}</td>
                                            </tr>
                                        `;
                                    } else {
                                        html_date = `
                                            <tr>
                                                <th>{{ Str::headline('Tanggal') }}</th>
                                                <td>${value.letter_date_start} - ${value.letter_date_end}</td>
                                            </tr>
                                        `;
                                    }

                                    $('#accordionIzin').append(`
                                        <div class="accordion-item">
                                            <h2 class="accordion-header" id="izin-${izinId}">
                                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#izin${izinId}" aria-expanded="false" aria-controls="izin${izinId}">
                                                   ${value.letter_date_start} - ${value.letter_date_end}
                                                </button>
                                            </h2>
                                            <div id="izin${izinId}" class="accordion-collapse collapse" aria-labelledby="izin-${izinId}" data-bs-parent="#accordionIzin">
                                                <div class="accordion-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <table class="table table-striped" id="izin-table">
                                                                <thead class="bg-dark">
                                                                    <th></th>
                                                                    <th></th>
                                                                </thead>
                                                                <tbody id="izin-table-body">
                                                                    <tr>
                                                                        <th>{{ Str::headline('type') }}</th>
                                                                        <td>Izin tidak masuk kerja</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ Str::headline('Nama Pegawai') }}</th>
                                                                        <td>${value.employee.name}</td>
                                                                    </tr>
                                                                    ${html_date}
                                                                    <tr>
                                                                        <th>{{ Str::headline('Alasan') }}</th>
                                                                        <td>${value.cause}</td>
                                                                    </tr>
                                                                    <tr>
                                                                        <th>{{ Str::headline('Note') }}</th>
                                                                        <td>${note}</td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `);
                                    izinId++;
                                });
                                $('#absenceDays').val(izin_amount);
                            } else {
                                $('#emptyIzin').show();
                                $('#absenceDays').val(0);
                            }
                            handleOnChangeAmountInput();
                        }
                    });
                }
            }

            function handleOnChangeAmountInput() {
                var workDays = parseInt($('#workDays').val() || '0');
                var workDaysTotal = parseInt($('#workDaysTotal').val() || '0');
                var alphaDays = parseInt($('#alphaDays').val() || '0');

                if (cuti_amount + izin_amount > 0) {
                    workDaysTotal = workDaysTotal - (cuti_amount + izin_amount);
                    $('#alphaDays').val(workDaysTotal - workDays);
                } else {
                    $('#alphaDays').val(workDaysTotal - workDays);
                }

                sumSalary();
            }

            function handleOnChangeEmployeeAndPeriod() {
                getCuti();
                getIzin();
                handleOnChangeAmountInput();
            }

            function getSalaryItemDetail(e, type) {
                let id = $(e).val();

                if (id == '' || id == null) {
                    return false;
                }
                $.ajax({
                    url: `${base_url}/salary-item/${id}`,
                    method: "GET",
                    success: function(res) {
                        if (type == "allowance") {
                            $('#allowanceName').val(res.data.name);
                            $('#allowancePercentage').val(formatRupiahWithDecimal(res.data.percentage));
                            if (res.data.percentage > 0) {
                                let amountPercentage = res.data.percentage * formatThousandToFloat($('#baseSalary').val()) / 100;
                                $('#allowanceAmount').val(formatRupiahWithDecimal(amountPercentage));
                                $('#allowanceAmount').attr('readonly', true);
                            } else {
                                $('#allowanceAmount').attr('readonly', false);
                            }
                            sumAllowance();
                        } else if (type == "fee") {
                            $('#feeName').val(res.data.name);
                            $('#feePercentage').val(formatRupiahWithDecimal(res.data.percentage));
                            if (res.data.percentage > 0) {
                                let amountPercentage = res.data.percentage * formatThousandToFloat($('#baseSalary').val()) / 100;
                                $('#feeAmount').val(formatRupiahWithDecimal(amountPercentage));
                                $('#feeAmount').attr('readonly', true);
                            } else {
                                $('#feeAmount').attr('readonly', false);
                            }
                            sumFee();
                        } else {
                            $('#deductionName').val(res.data.name);
                            $('#deductionPercentage').val(formatRupiahWithDecimal(res.data.percentage));
                            if (res.data.percentage > 0) {
                                let amountPercentage = res.data.percentage * formatThousandToFloat($('#baseSalary').val()) / 100;
                                $('#deductionAmount').val(formatRupiahWithDecimal(amountPercentage));
                                $('#deductionAmount').attr('readonly', true);
                            } else {
                                $('#deductionAmount').attr('readonly', false);
                            }
                            sumDeduction();
                        }
                    }
                });
            }

            function setPegawai(e) {
                $('#user').val($(e).val());
                $.ajax({
                    url: `/employee/${$(e).val()}`,
                    method: "GET",
                    success: function(res) {
                        if (res.data.non_taxable_income == null) {
                            showAlert('Peringatan', 'Pegawai belum memiliki status pernikahan. Silahkan lengkapi data pegawai terlebih dahulu.', 'warning');

                            $('#non_taxable_income').text('');
                            $('#npwp').text('');
                            return false;
                        }
                        $('#non_taxable_income').text(res.data.non_taxable_income.note ?? '');
                        $('#npwp').text(res.data.npwp ?? 'Tidak ada NPWP');

                    }
                });
                handleOnChangeEmployeeAndPeriod();
            }

            function sumAllowance() {
                var allowanceAmount = $('#allowanceAmount').val();
                var allowancePercentage = $('#allowancePercentage').val();
                var allowanceQty = $('#allowanceQty').val();
                var allowanceTotal = $('#allowanceTotal');
                let baseSalary = $('#baseSalary').val();

                allowancePercentageValue = thousandToFloat(allowancePercentage);
                if (allowancePercentageValue > 0) {
                    allowanceAmount = allowancePercentageValue * formatThousandToFloat(baseSalary) / 100;
                    $('#allowanceAmount').val(formatRupiahWithDecimal(allowanceAmount));
                }

                let total = parseInt(allowanceQty) * parseFloat(formatThousandToFloat(allowanceAmount)).toFixed(2);
                allowanceTotal.val(formatRupiahWithDecimal(total));
            }

            function sumFee() {
                var feeAmount = $('#feeAmount').val();
                var feePercentage = $('#feePercentage').val();
                var feeQty = $('#feeQty').val();
                var feeTotal = $('#feeTotal');
                let baseSalary = $('#baseSalary').val();

                feePercentageValue = thousandToFloat(feePercentage);
                if (feePercentageValue > 0) {
                    feeAmount = feePercentageValue * formatThousandToFloat(baseSalary) / 100;
                    $('#feeAmount').val(formatRupiahWithDecimal(feeAmount));
                }

                let total = parseInt(feeQty) * parseFloat(formatThousandToFloat(feeAmount)).toFixed(2);
                feeTotal.val(formatRupiahWithDecimal(total));
            }

            function sumDeduction() {
                var deductionAmount = $('#deductionAmount').val();
                var deductionPercentage = $('#deductionPercentage').val();
                var deductionQty = $('#deductionQty').val();
                var deductionTotal = $('#deductionTotal');
                let baseSalary = $('#baseSalary').val();

                deductionPercentageValue = thousandToFloat(deductionPercentage);
                if (deductionPercentageValue > 0) {
                    deductionAmount = deductionPercentageValue * formatThousandToFloat(baseSalary) / 100;
                    $('#deductionAmount').val(formatRupiahWithDecimal(deductionAmount));
                }

                let total = parseInt(deductionQty) * parseFloat(formatThousandToFloat(deductionAmount)).toFixed(2);
                deductionTotal.val(formatRupiahWithDecimal(total));
            }

            function addAllowance() {
                var allowanceName = $('#allowanceName').val();
                var allowanceAmount = $('#allowanceAmount').val();
                var allowanceQty = $('#allowanceQty').val();
                var allowanceTotal = $('#allowanceTotal').val();
                var allowancePercentage = $('#allowancePercentage').val();

                if (allowanceName == '') {
                    alert('Nama tunjangan belum diisi!');
                } else if (formatThousandToFloat(allowanceAmount) == 0 || allowanceAmount == '') {
                    alert('Besaran tunjangan belum diisi!');
                } else if (parseInt(allowanceQty) == 0 || allowanceQty == '') {
                    alert('Jumlah tunjangan belum diisi!');
                } else {
                    var data = `<div id="allowance${allowanceCount}" class="row mb-1">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="hidden" name="allowance_detail_type[]" value="other">
                                        <input type="text" class="form-control" name="allowance_detail_name[]" value="${ucfirst(allowanceName)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control allowance-percentage${allowanceCount} text-end" name="allowance_detail_percentage[]" value="${allowancePercentage}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form allowance-amount${allowanceCount} text-end" name="allowance_detail_amount[]" value="${formatRupiahWithDecimal(allowanceAmount)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control allowance-qty${allowanceCount} text-end" name="allowance_detail_qty[]" value="${allowanceQty}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form allowance-total${allowanceCount} text-end" name="allowance_detail_total[]" value="${formatRupiahWithDecimal(allowanceTotal)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger" onclick="deleteAllowance(${allowanceCount})">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    $('#allowanceDetail').append(data);

                    initCommasForm();
                    sumSalary();

                    $('#allowanceName').val('');
                    $('#allowanceAmount').val(0);
                    $('#allowanceQty').val(0);
                    $('#allowancePercentage').val(0);
                    $('#allowanceTotal').val(0);
                    $('#allowanceId').val('').trigger('change');

                    allowanceCount++;
                }

            }

            function deleteAllowance(id) {
                let row = $(`#allowance${id}`);
                row.remove();
                sumSalary();
            }

            function addFee() {
                var feeName = $('#feeName').val();
                var feeAmount = $('#feeAmount').val();
                var feeQty = $('#feeQty').val();
                var feeTotal = $('#feeTotal').val();
                var feePercentage = $('#feePercentage').val();

                if (feeName == '') {
                    alert('Nama tunjangan belum diisi!');
                } else if (formatThousandToFloat(feeAmount) == 0 || feeAmount == '') {
                    alert('Besaran tunjangan belum diisi!');
                } else if (parseInt(feeQty) == 0 || feeQty == '') {
                    alert('Jumlah tunjangan belum diisi!');
                } else {
                    var data = `<div id="fee${feeCount}" class="row mb-1">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="hidden" name="fee_detail_type[]" value="other">
                                        <input type="text" class="form-control" name="fee_detail_name[]" value="${ucfirst(feeName)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control fee-percentage${feeCount} text-end" name="fee_detail_percentage[]" value="${feePercentage}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form fee-amount${feeCount} text-end" name="fee_detail_amount[]" value="${formatRupiahWithDecimal(feeAmount)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control fee-qty${feeCount} text-end" name="fee_detail_qty[]" value="${feeQty}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form fee-total${feeCount} text-end" name="fee_detail_total[]" value="${formatRupiahWithDecimal(feeTotal)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger" onclick="deleteFee(${feeCount})">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    $('#feeDetail').append(data);

                    initCommasForm();
                    sumSalary();

                    $('#feeName').val('');
                    $('#feeAmount').val(0);
                    $('#feeQty').val(0);
                    $('#feePercentage').val(0);
                    $('#feeTotal').val(0);
                    $('#feeId').val('').trigger('change');

                    feeCount++;
                }
            }

            function deleteFee(id) {
                let row = $(`#fee${id}`);
                row.remove();
                sumSalary();
            }

            function addDeduction() {
                var deductionName = $('#deductionName').val();
                var deductionPercentage = $('#deductionPercentage').val();
                var deductionAmount = $('#deductionAmount').val();
                var deductionQty = $('#deductionQty').val();
                var deductionTotal = $('#deductionTotal').val();

                if (deductionName == '') {
                    alert('Nama potongan belum diisi!');
                } else if (formatThousandToFloat(deductionAmount) == 0 || deductionAmount == '') {
                    alert('Besaran potongan belum diisi!');
                } else if (parseInt(deductionQty) == 0 || deductionQty == '') {
                    alert('Jumlah potongan belum diisi!');
                } else {
                    var data = `<div id="deduction${deductionCount}" class="row mb-1">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="hidden" name="deduction_detail_type[]" value="other">
                                        <input type="text" class="form-control" name="deduction_detail_name[]" value="${ucfirst(deductionName)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control deduction-percentage${deductionCount} text-end" name="deduction_detail_percentage[]" value="${deductionPercentage}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form deduction-amount${deductionCount} text-end" name="deduction_detail_amount[]" value="${formatRupiahWithDecimal(deductionAmount)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control deduction-qty${deductionCount} text-end" name="deduction_detail_qty[]" value="${deductionQty}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form deduction-total${deductionCount} text-end" name="deduction_detail_total[]" value="${formatRupiahWithDecimal(deductionTotal)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger" onclick="deleteDeduction(${deductionCount})">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    $('#deductionDetail').append(data);

                    initCommasForm();
                    sumSalary();

                    $('#deductionName').val('');
                    $('#deductionAmount').val(0);
                    $('#deductionQty').val(0);
                    $('#deductionPercentage').val(0);
                    $('#deductionTotal').val(0);
                    $('#deductionId').val('').trigger('change');

                    deductionCount++;
                }
            }

            function deleteDeduction(id) {
                let row = $(`#deduction${id}`);
                row.remove();
                sumSalary();
            }

            function sumSalary() {
                var periodType = $('#periodType').val();
                var baseSalary = formatThousandToFloat($('#baseSalary').val());
                var workDays = parseInt($('#workDays').val() || '0');
                var workDaysTotal = parseInt($('#workDaysTotal').val() || '0');
                var alphaDays = parseInt($('#alphaDays').val() || '0');

                var brutto = $('#bruttoSalary');
                var netto = $('#nettoSalary');

                var alpha_amount = 0;
                var bruttoTotal = 0;
                var nettoTotal = 0;
                var feeAmount = 0;
                var allowanceAmount = 0;
                var deductionAmount = 0;

                if (baseSalary > 0) {
                    nettoTotal = baseSalary;
                }

                if (alphaDays > 0 && $('#is_absence_calculated').is(':checked')) {
                    if (periodType == 'mingguan') {
                        tmpBaseSalary = baseSalary;
                        bruttoTotal = baseSalary * (workDaysTotal || 0);
                        nettoTotal = baseSalary * (workDays || 0);
                    } else {
                        tmpBaseSalary = Math.floor(baseSalary / workDaysTotal);
                    }
                    let totalAlpha = tmpBaseSalary * alphaDays;
                    addDayOffDeduction(tmpBaseSalary, alphaDays, totalAlpha);
                } else {
                    if (periodType == 'mingguan') {
                        bruttoTotal = baseSalary * (workDaysTotal || 0);
                        nettoTotal = baseSalary * (workDays || 0);
                    }
                    $('.day-off').remove();
                }

                var arrayFee = $("input[name='fee_detail_total[]']").map(function() {
                    return formatThousandToFloat($(this).val());
                }).get();

                for (let i = 0; i < arrayFee.length; i++) {
                    feeAmount += arrayFee[i];
                }

                bruttoTotal += feeAmount;

                var arrayAllowance = $("input[name='allowance_detail_total[]']").map(function() {
                    return formatThousandToFloat($(this).val());
                }).get();

                for (let i = 0; i < arrayAllowance.length; i++) {
                    allowanceAmount += arrayAllowance[i];
                }

                bruttoTotal += allowanceAmount;

                var arrayDeduction = $("input[name='deduction_detail_total[]']").map(function() {
                    return formatThousandToFloat($(this).val());
                }).get();

                for (let i = 0; i < arrayDeduction.length; i++) {
                    deductionAmount += arrayDeduction[i];
                }

                nettoTotal = bruttoTotal - deductionAmount;

                netto.val(formatRupiahWithDecimal(nettoTotal));
                brutto.val(formatRupiahWithDecimal(bruttoTotal));
            }

            function addDayOffDeduction(amount, qty, total) {
                if (!$('.day-off')[0]) {
                    var data = `<div class="row day-off mb-1">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="hidden" name="deduction_detail_type[]" value="day-off">
                                        <input type="text" class="form-control" name="deduction_detail_name[]" value="Alpha" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control day-off-percentage text-end" name="deduction_detail_percentage[]" value="0" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form day-off-amount text-end" name="deduction_detail_amount[]" value="${formatRupiahWithDecimal(amount)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control day-off-qty text-end" name="deduction_detail_qty[]" value="${qty}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form day-off-total text-end" name="deduction_detail_total[]" value="${formatRupiahWithDecimal(total)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-danger" onclick="$('.day-off').remove()">
                                            <i class="fa fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>`;
                    $('#deductionDetail').append(data);
                    initCommasForm();
                } else {
                    $('.day-off-amount').val(formatRupiahWithDecimal(amount));
                    $('.day-off-qty').val(qty);
                    $('.day-off-total').val(formatRupiahWithDecimal(total));
                }
            }

            function submit() {
                var form = $('#form-data');
                var periodType = $('#periodType');
                var user = $('#user');
                var baseSalary = $('#baseSalary').val() || 0;
                var bruttoSalary = $('#bruttoSalary').val() || 0;
                var workDaysTotal = $('#workDaysTotal').val() || 0;
                var workDays = $('#workDays').val() || 0;

                if (periodType.val() == '') {
                    alert('Periode belum dipilih!');
                } else if (user.val() == '') {
                    alert('Pegawai belum dipilih!');
                } else if (baseSalary == 0) {
                    alert('Gaji pokok belum diisi!');
                } else if (workDaysTotal == 0) {
                    alert('Jumlah hari kerja belum diisi!');
                } else if (workDays == 0) {
                    alert('Jumlah masuk kerja belum diisi!');
                } else if (formatThousandToFloat(baseSalary) != formatThousandToFloat(bruttoSalary)) {
                    alert('Gaji kotor tidak sesuai dengan take home pay!');
                } else {
                    form.submit();
                }
            }

            function replaceDot(value) {
                return value.toString().replace(/\./g, "");
            }
        </script>
    @endcan
    <script>
        initSelect2Search(
            `allowanceId`,
            `${base_url}/select/salary-item`, {
                id: "id",
                text: "name",
            },
            0, {
                type: "tunjangan",
            },
        );

        initSelect2Search(
            `feeId`,
            `${base_url}/select/salary-item`, {
                id: "id",
                text: "name",
            },
            0, {
                type: "upah",
            },
        );

        initSelect2Search(
            `deductionId`,
            `${base_url}/select/salary-item`, {
                id: "id",
                text: "name",
            },
            0, {
                type: "potongan",
            },
        );

        $('#baseSalary').keyup(function() {
            var baseSalary = $(this).val();

            let feeNames = $('input[name="fee_detail_name[]"]');
            let feePercentages = $('input[name="fee_detail_percentage[]"]');
            let feeAmounts = $('input[name="fee_detail_amount[]"]');
            let feeQtys = $('input[name="fee_detail_qty[]"]');
            let feeTotals = $('input[name="fee_detail_total[]"]');

            feeNames.map(function(index, value) {
                let percentage = formatThousandToFloat(feePercentages[index].value);
                let amount = formatThousandToFloat(feeAmounts[index].value);
                let qty = formatThousandToFloat(feeQtys[index].value);
                let total = formatThousandToFloat(feeTotals[index].value);

                if (percentage > 0) {
                    amount = percentage * formatThousandToFloat(baseSalary) / 100;
                    feeAmounts[index].value = formatRupiahWithDecimal(amount);
                }

                total = parseInt(qty) * parseFloat(amount).toFixed(2);

                feeAmounts[index].value = formatRupiahWithDecimal(amount);
                feeTotals[index].value = formatRupiahWithDecimal(total);
            });

            let allowanceNames = $('input[name="allowance_detail_name[]"]');
            let allowancePercentages = $('input[name="allowance_detail_percentage[]"]');
            let allowanceAmounts = $('input[name="allowance_detail_amount[]"]');
            let allowanceQtys = $('input[name="allowance_detail_qty[]"]');
            let allowanceTotals = $('input[name="allowance_detail_total[]"]');

            allowanceNames.map(function(index, value) {
                let percentage = formatThousandToFloat(allowancePercentages[index].value);
                let amount = formatThousandToFloat(allowanceAmounts[index].value);
                let qty = formatThousandToFloat(allowanceQtys[index].value);
                let total = formatThousandToFloat(allowanceTotals[index].value);

                if (percentage > 0) {
                    amount = percentage * formatThousandToFloat(baseSalary) / 100;
                    allowanceAmounts[index].value = formatRupiahWithDecimal(amount);
                }

                total = parseInt(qty) * parseFloat(amount).toFixed(2);

                allowanceAmounts[index].value = formatRupiahWithDecimal(amount);
                allowanceTotals[index].value = formatRupiahWithDecimal(total);
            });

            let deductionDetailTipes = $('input[name="deduction_detail_type[]"]');
            let deductionNames = $('input[name="deduction_detail_name[]"]');
            let deductionPercentages = $('input[name="deduction_detail_percentage[]"]');
            let deductionAmounts = $('input[name="deduction_detail_amount[]"]');
            let deductionQtys = $('input[name="deduction_detail_qty[]"]');
            let deductionTotals = $('input[name="deduction_detail_total[]"]');

            deductionNames.map(function(index, value) {
                let percentage = formatThousandToFloat(deductionPercentages[index].value);
                let amount = formatThousandToFloat(deductionAmounts[index].value);
                let qty = formatThousandToFloat(deductionQtys[index].value);
                let total = formatThousandToFloat(deductionTotals[index].value);
                let type = deductionDetailTipes[index].value;

                if (type != 'income-tax') {
                    if (percentage > 0) {
                        amount = percentage * formatThousandToFloat(baseSalary) / 100;
                        deductionAmounts[index].value = formatRupiahWithDecimal(amount);
                    }

                    total = parseInt(qty) * parseFloat(formatThousandToFloat(amount)).toFixed(2);
                    deductionTotals[index].value = formatRupiahWithDecimal(total);
                }

            });

            sumSalary();
        });

        $('#feePercentage').keyup(function() {
            if (thousandToFloat($(this).val()) > 0) {
                $('#feeAmount').attr('readonly', true);
            } else {
                $('#feeAmount').attr('readonly', false);
            }
        })

        $('#allowancePercentage').keyup(function() {
            if (thousandToFloat($(this).val()) > 0) {
                $('#allowanceAmount').attr('readonly', true);
            } else {
                $('#allowanceAmount').attr('readonly', false);
            }
        })

        $('#deductionPercentage').keyup(function() {
            if (thousandToFloat($(this).val()) > 0) {
                $('#deductionAmount').attr('readonly', true);
            } else {
                $('#deductionAmount').attr('readonly', false);
            }
        })
    </script>
    <script>
        const calculateIncomeTax = () => {
            $.ajax({
                url: `/payroll/calculate-income-tax`,
                method: "POST",
                data: {
                    'employee_id': $('#user').val(),
                    'base_salary': $('#baseSalary').val(),
                    '_token': token,
                },
                success: function(res) {
                    $('.income-tax').remove();
                    if (res.income_tax_result) {
                        var data = `<div class="row income-tax mb-1">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="hidden" name="deduction_detail_type[]" value="income-tax">
                                        <input type="text" class="form-control" name="deduction_detail_name[]" value="PPh 21" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control income-tax-percentage text-end" name="deduction_detail_percentage[]" value="${formatRupiahWithDecimal(res.income_tax_percent)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form income-tax-amount text-end" name="deduction_detail_amount[]" value="${formatRupiahWithDecimal(res.income_tax_result)}" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-1">
                                    <div class="form-group">
                                        <input type="text" class="form-control income-tax-qty text-end" name="deduction_detail_qty[]" value="1" readonly>
                                    </div>
                                </div>
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <input type="text" class="form-control commas-form income-tax-total text-end" name="deduction_detail_total[]" value="${formatRupiahWithDecimal(res.income_tax_result)}" readonly>
                                    </div>
                                </div>
                                 <div class="col-sm-2">
                                </div>
                            </div>`;
                        $('#deductionDetail').append(data);
                        sumSalary()
                    } else {
                        $('.income-tax').remove();
                        sumSalary();
                    }
                }
            });
        }
    </script>
    <script>
        sidebarMenuOpen('#hrd');
        sidebarMenuOpen('#payroll-sidebar');
        sidebarActive('#payroll')
    </script>
@endsection
