@extends('layouts.admin.layout.index')

@php
    $main = 'receive-payment';
    $title = 'giro masuk';
@endphp

@section('title', Str::headline("tambah $main") . ' - ')

@section('breadcrumbs')
    <div class="box">
        <div class="box-body">
            <nav style="--bs-breadcrumb-divider: '/';" aria-label="breadcrumb">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item">
                        <a href="{{ route('admin.index') }}">Dashboard</a>
                    </li>
                    <li class="breadcrumb-item active">
                        {{ Str::headline('tambah ' . $title) }}
                    </li>
                </ol>
            </nav>
        </div>
    </div>
@endsection

@section('content')
    <x-card-data-table title="{{ 'tambah ' . $title }}">
        <x-slot name="table_content">
            @include('components.validate-error')
            <form action="{{ route('admin.' . $main . '.store') }}" method="post" enctype="multipart/form-data" id="form-data">
                @csrf
                <div class="row">
                    <div class="col-md-3">
                        <x-select name="branch_id" id="branch_id" label="Branch" required autofocus>
                            <option value="{{ get_current_branch()->id }}" selected>{{ get_current_branch()->name }}</option>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-select name="pay_from" id="pay_from" label="terima dari" required autofocus onchange="set_pay_from($(this))">
                            <option value="customer">Customer</option>
                            <option value="other">Lainnya</option>
                        </x-select>
                    </div>
                    <div class="col-md-3" id="customer-form">
                        <x-select name="customer_id" id="customer_id" label="customer" required autofocus>
                        </x-select>
                    </div>
                    <div class="col-md-3 d-none" id="other-form">
                        <div class="form-group">
                            <x-input type="text" id="from_name" name="from_name" label="terima dari" required value="" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="date" name="date" onchange="checkClosingPeriod($(this))" label="tanggal" required class="datepicker-input" value="{{ date('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="due_date" name="due_date" label="tanggal jatuh tempo" required class="datepicker-input" value="{{ date('d-m-Y') }}" />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="cheque_no" name="cheque_no" label="no cheque" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="from_bank" name="from_bank" label="BG mundur bank" required />
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="realization_bank" name="realization_bank" label="bank pencairan" required />
                        </div>
                    </div>
                    <div class="col-md-12"></div>
                    <div class="col-md-3">
                        <x-select name="currency_id" id="currency_id" label="currency" required autofocus>
                            <option value="{{ get_local_currency()->id }}" selected>{{ get_local_currency()->nama }}</option>
                        </x-select>
                    </div>
                    <div class="col-md-3">
                        <x-input type="text" name="exchange_rate" class="commas-form text-end" label="kurs" id="exchange_rate" value="1" required readonly />
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <x-input type="text" id="amount" name="amount" class="commas-form" label="jumlah" required />
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
@endsection

@section('js')
    <script src="{{ asset('js/form/select2search.js') }}"></script>
    <script src="{{ asset('js/helpers/helpers.js') }}"></script>
    <script src="{{ asset('js/admin/receive-payment/transaction.js') }}"></script>

    <script>
        sidebarMenuOpen('#finance-main-sidebar');
        sidebarActive('#incoming-payment-sidebar');
        sidebarActive('#receive-payment')
        checkClosingPeriod($('#date'))

        initSelect2Search(`customer_id`, `{{ route('admin.select.customer') }}`, {
            id: "id",
            text: "nama"
        }, 0, {
            branch_id: function() {
                return $('#branch_id').val();
            },
        });

        initSelect2Search(`currency_id`, `{{ route('admin.select.currency') }}`, {
            id: "id",
            text: "kode,nama"
        });

        $('#currency_id').change(function(e) {
            e.preventDefault();
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

        $('#from_name').attr('required', false);
        const set_pay_from = (e) => {
            if ($(e).val() == "customer") {
                $('#customer-form').removeClass('d-none');
                $('#customer_id').attr('required', true);
                $('#other-form').addClass('d-none');
                $('#from_name').attr('required', false);
            } else {
                $('#customer-form').addClass('d-none');
                $('#customer_id').attr('required', false);
                $('#other-form').removeClass('d-none');
                $('#from_name').attr('required', true);
            }
        }

        $('#form-data').submit(function(e) {
            e.preventDefault();
            var data = new FormData(this);

            $('.is-invalid').removeClass('is-invalid');
            $('.invalid-feedback').remove();

            $.ajax({
                url: $(this).attr('action'),
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                type: 'POST',
                data: data,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#form-data').unbind('submit').submit();
                },
                error: function(response) {
                    $('#form-data').find('button[type="submit"]').removeAttr('disabled');
                    if (response.status == 422) {
                        var errors = response.responseJSON.errors;
                        for (var key in errors) {
                            if (errors.hasOwnProperty(key)) {
                                console.log(key);
                                let element_key = key;
                                if (element_key.includes('.')) {
                                    element_key = key.split('.');
                                    element_key = element_key[0] + '[' + element_key[1] + ']';
                                }
                                var element = $('#form-data').find('[name="' + element_key + '"]');


                                element.addClass('is-invalid');
                                element.siblings('.invalid-feedback').remove();
                                element.after('<div class="invalid-feedback">' + errors[key][0] + '</div>');

                            }
                        }

                    }

                    if (response.status == "400") {
                        alert(response.responseJSON.message);
                    }
                }
            });
        });
    </script>

    @if (get_current_branch()->is_primary == 1)
        <script>
            initSelect2Search(`branch_id`, `{{ route('admin.select.branch') }}`, {
                id: "id",
                text: "name"
            });
        </script>
    @endif
@endsection
