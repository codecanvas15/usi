<div class="row">
    <div class="col-md-12 mb-3">
        <h3>Finance Dashboard</h3>
    </div>
    <div class="col-xl-6 col-12">
        <div class="box piutang-jatuh-tempo">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="box-title">Piutang Jatuh Tempo</h4>
                    </div>
                    <div class="col">
                        <x-input id="invoice-date" type="text" class="datepicker-input" value="{{ date('d-m-Y') }}" placeholder="Pilih Tanggal" label="per tanggal" />
                    </div>
                </div>
            </div>
            <div class="box-body pt-0" style="min-height: 400px;max-height: 400px;overflow-y: scroll;" id="invoice_wrapper">
                <div class="">
                    <table width="100%" class="table">
                        <tbody id="invoice_due_data"></tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="20%">
                                Total
                            </td>
                            <td width="80%" style="text-align: right">
                                Rp <span class="piutang-jatuh-tempo-total">NaN</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-12">
        <div class="box hutang-jatuh-tempo">
            <div class="box-header with-border">
                <div class="row">
                    <div class="col-md-8">
                        <h4 class="box-title">Hutang Jatuh Tempo</h4>
                    </div>
                    <div class="col">
                        <x-input id="supplier-invoice-date" type="text" class="datepicker-input" value="{{ date('d-m-Y') }}" placeholder="Pilih Tanggal" label="per tanggal" />
                    </div>
                </div>
            </div>
            <div class="box-body pt-0" style="min-height: 400px;max-height: 400px;overflow-y: scroll;" id="supplier_invoice_wrapper">
                <div class="">
                    <table width="100%" class="table">
                        <tbody id="supplier_invoice_due_data"></tbody>
                    </table>
                </div>
            </div>
            <div class="box-footer">
                <table width="100%">
                    <tbody>
                        <tr>
                            <td width="20%">
                                Total
                            </td>
                            <td width="80%" style="text-align: right">
                                Rp <span class="hutang-jatuh-tempo-total">NaN</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="col-xl-6 col-12">
        <div class="box coa-internal-bank">
            <div class="box-header with-border">
                <h4 class="box-title mb-2">Saldo Bank Internal</h4>
                <input id="coaInternalDate" type="text" name="from_date" class="datepicker-input form-control mb-2" value="" placeholder="Pilih Tanggal" required="" style="max-width: 440px">
                <input id="coaInternalBankName" type="text" name="coaInternalBankName" class="form-control" value="" placeholder="Nama Bank Internal" required="" style="max-width: 440px">
            </div>
            <div class="box-body pt-0" style="min-height: 400px;max-height: 400px;overflow-y: scroll;" id="coa_internal_bank_wrapper">
                <div class="">
                    <table width="100%" class="table">
                        <tbody id="coa_internal_bank_wrapper_data"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script src="{{ asset('js/numeral.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            let invoice_count = 0;
            let piutang_total = 0;
            let hutang_total = 0;

            const refreshDataInvoice = () => {
                invoice_count = 0;
                piutang_total = 0;
                $('#invoice_due_data').html('');
                getDataInvoice();
            }

            const getDataInvoice = () => {
                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-finance-invoice-due') }}",
                    data: {
                        offset: invoice_count,
                        date: function() {
                            return $('#invoice-date').val();
                        },
                    },
                    success: function(data) {
                        let invoice_dues = '';
                        $.each(data.invoice_dues, function(index, value) {
                            let exchange_html = '';
                            if (value.is_exchange) {
                                exchange_html = `<p class="text-end mb-0">${value.outstanding_exchanged_formatted}</p>`;
                            }
                            invoice_dues += `<tr>
                                <td>
                                    ${value.badge}
                                    <p class="text-break mt-1 mb-0 fw-bold">${value.customer_name}</p>
                                    <small class="text-capitalize">${value.code}</small>
                                    <br>
                                    <p class="text-end mb-0">${value.outstanding}</p>
                                    ${exchange_html}
                                    <p class="text-info mb-0">Jatuh Tempo ${value.due_date}</p>
                                </td>
                                <td class="text-end">
                                    <a href="${value.link}" target="_blank">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>`;
                            piutang_total += parseFloat(value.outstanding_exchanged);
                        });
                        invoice_count += data.invoice_dues.length;
                        $('.piutang-jatuh-tempo-total').text(numeral(piutang_total).format('0,0.00'));


                        $('#invoice_due_data').append(invoice_dues);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            $('#invoice-date').change(function() {
                refreshDataInvoice();
            });

            const refreshDataSupplierInvoice = () => {
                supplier_invoice_count = 0;
                hutang_total = 0;
                $('#supplier_invoice_due_data').html('');
                getDataSupplierInvoice();
            }

            $('#supplier-invoice-date').change(function() {
                refreshDataSupplierInvoice();
            });


            let supplier_invoice_count = 0;
            const getDataSupplierInvoice = () => {
                $.ajax({
                    url: "{{ route('admin.index.get-data-dashboard-finance-supplier-invoice-due') }}",
                    data: {
                        offset: supplier_invoice_count,
                        date: function() {
                            return $('#supplier-invoice-date').val();
                        },
                    },
                    success: function(data) {
                        let supplier_invoice_due = '';
                        $.each(data.supplier_invoice_dues, function(index, value) {
                            let exchange_html = '';
                            if (value.is_exchange) {
                                exchange_html = `<p class="text-end mb-0">${value.outstanding_exchanged_formatted}</p>`;
                            }

                            supplier_invoice_due += `<tr>
                                <td>
                                    ${value.badge}
                                    <p class="text-break mt-1 mb-0 fw-bold">${value.vendor_name}</p>
                                    <small class="text-capitalize">${value.code}</small>
                                    <br>
                                    <p class="text-end mb-0">${value.outstanding}</p>
                                    ${exchange_html}
                                    <p class="text-info mb-0">Jatuh Tempo ${value.due_date}</p>
                                </td>
                                <td class="text-end">
                                    <a href="${value.link}" target="_blank">
                                        <i class="fa fa-chevron-right"></i>
                                    </a>
                                </td>
                            </tr>`;
                            hutang_total += parseFloat(value.outstanding_exchanged);
                        });
                        $('.hutang-jatuh-tempo-total').text(numeral(hutang_total).format('0,0.00'));

                        supplier_invoice_count += data.supplier_invoice_dues.length;

                        $('#supplier_invoice_due_data').append(supplier_invoice_due);
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            }

            const coaBankInternal = () => {
                $.ajax({
                    url: "{{ route('admin.coa-bank-internal.dashboard') }}?offset=" + supplier_invoice_count,
                    data: {
                        from_date: $('#coaInternalDate').val(),
                        bank_name: $('#coaInternalBankName').val(),
                    },
                    success: function(response) {
                        let coa_internal_bank = '';
                        $.each(response.data, function(index, value) {
                            coa_internal_bank += `<tr>
                                <td>
                                    <p class="text-break mt-1 mb-0 fw-bold">${value.coa.name}</p>
                                    <small class="text-capitalize">${value.bank_internal ? value.bank_internal.no_rekening : '-'}</small>
                                    <p class="text-end mb-0">Rp ${parseFloat(value.amount_before_exchanged).toLocaleString()}</p>
                                    <small>${value.bank_internal ? value.bank_internal.nama_bank : 'No Bank Data'}</small>
                                </td>
                                <td class="text-end">
                                </td>
                            </tr>`;
                        });

                        $('#coa_internal_bank_wrapper_data').html(coa_internal_bank);
                    },
                    error: function(error) {
                        console.log("Error:", error);
                    }
                });
            };
            coaBankInternal();
            getDataInvoice();
            getDataSupplierInvoice();

            $('#coaInternalDate').change(function() {
                coaBankInternal();
            });

            let timeout = null;

            $('#coaInternalBankName').on('input', function() {
                clearTimeout(timeout); // Clear previous timeout
                timeout = setTimeout(function() {
                    coaBankInternal();
                }, 500);
            });

            $('#supplier_invoice_wrapper').bind('scroll', check_scroll);
            $('#invoice_wrapper').bind('scroll', check_scroll);

            function check_scroll(e) {
                var elem = $(e.currentTarget);
                console.log(elem[0].scrollHeight - elem.scrollTop());
                if (elem[0].scrollHeight - elem.scrollTop() == elem.outerHeight()) {
                    if (elem.attr('id') == 'invoice_wrapper') {
                        getDataInvoice();
                    } else if (elem.attr('id') == 'supplier_invoice_wrapper') {
                        getDataSupplierInvoice();
                    }
                }
            }

            $('#reload-data').click(function(e) {
                e.preventDefault();
                invoice_count = 0;
                supplier_invoice_count = 0;
                $('#supplier_invoice_due_data').html('');
                $('#invoice_due_data').html('');

                getData();
            });
        });
    </script>
@endpush
