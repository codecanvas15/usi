<div class="box shadow">
    <div class="row">
        <div class="col-12">
            <div class="box shadow-none px-3 bg-white text-center p-1">
                <h3>Customer Invoice Jatuh Tempo</h3>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xl-6">
            <div>
                <div class="box-header with-border text-center">
                    <h4 class="box-title text-center">Invoice Trading</h4>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 dash-table" id="po-today">
                            <thead>
                                <th>Kode</th>
                                <th>Tanggal Berakhir</th>
                            </thead>
                            <tbody id="inv-trading">

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div>
                <div class="box-header with-border text-center">
                    <h4 class="box-title text-center">Invoice General</h4>
                </div>
                <div class="box-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0 dash-table">
                            <thead>
                                <th>Kode</th>
                                <th>Tanggal Berakhir</th>
                            </thead>
                            <tbody id="inv-general">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="box shadow">
    <div class="row">
        <div class="col-12">
            <div class="box shadow-none px-3 bg-white text-center p-1 mb-4">
                <h3>Purchase Invoice Jatuh Tempo</h3>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="row">
            <div class="col-xl-6">
                <div>
                    <div class="box-header with-border text-center">
                        <h4 class="box-title text-center">Purchase Invoice</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 dash-table" id="po-today">
                                <thead>
                                    <th>Kode</th>
                                    <th>Tanggal Berakhir</th>
                                </thead>
                                <tbody id="supp-inv">

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-6">
                <div>
                    <div class="box-header with-border text-center">
                        <h4 class="box-title text-center">Puchase Invoice (Non LPB)</h4>
                    </div>
                    <div class="box-body p-0">
                        <div class="table-responsive">
                            <table class="table mb-0 dash-table">
                                <thead>
                                    <th>Kode</th>
                                    <th>Tanggal Berakhir</th>
                                </thead>
                                <tbody id="supp-general">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(document).ready(function() {
            let invoiceTrading = []
            invoiceGeneral = []
            supplierInvoice = []
            supplierGeneralInvoice = []

            let limitTrading = 5
            limitGeneral = 5
            limitSupp = 5
            limitSuppGen = 5

            function getInvoiceAll() {
                $.ajax({
                    url: `{{ route('admin.index.get-data-dashboard-invoice') }}`,
                    type: 'GET',
                    success: function({
                        data
                    }) {
                        let {
                            customer,
                            supplier
                        } = data
                        let {
                            invoice_trading,
                            invoice_general
                        } = customer
                        let {
                            supplier_invoice,
                            supplier_invoice_general
                        } = supplier

                        invoiceTrading = invoice_trading
                        invoiceGeneral = invoice_general
                        supplierInvoice = supplier_invoice
                        supplierGeneralInvoice = supplier_invoice_general

                        displayInvoiceTrading()
                        displayInvoiceGeneral()
                        displaySupplierInvoice()
                        displaySupplierInvoiceGeneral()
                    }
                })
            }

            getInvoiceAll()

            function displayInvoiceTrading() {
                let htmlTable = ``;
                invoiceTrading.map((res, i) => {
                    htmlTable += `
                        <tr>
                            <td><a href="{{ route('admin.invoice-trading.index') }}/${res?.id}" target="_blank">${res?.kode}</a></td>
                            <td>${res?.due_date}</td>
                        </tr>
                    `
                })
                $(`#inv-trading`).html(htmlTable)
            }

            function displayInvoiceGeneral() {
                let htmlTable = ``;
                invoiceGeneral.map((res, i) => {
                    htmlTable += `
                        <tr>
                            <td><a href="{{ route('admin.invoice-general.index') }}/${res?.id}" target="_blank">${res?.code}</a></td>
                            <td>${res?.due_date}</td>
                        </tr>
                    `
                })
                $(`#inv-general`).html(htmlTable)
            }

            function displaySupplierInvoice() {
                let htmlTable = ``;
                supplierInvoice.map((res, i) => {
                    htmlTable += `
                        <tr>
                            <td><a href="{{ route('admin.supplier-invoice.index') }}/${res?.id}" target="_blank">${res?.code}</a></td>
                            <td>${res?.top_due_date}</td>
                        </tr>
                    `
                })
                $(`#supp-inv`).html(htmlTable)
            }

            function displaySupplierInvoiceGeneral() {
                let htmlTable = ``;
                supplierGeneralInvoice.map((res, i) => {
                    htmlTable += `
                        <tr>
                            <td><a href="{{ route('admin.supplier-invoice-general.index') }}/${res?.id}" target="_blank">${res?.code}</a></td>
                            <td>${res?.top_due_date}</td>
                        </tr>
                    `
                })
                $(`#supp-general`).html(htmlTable)
            }
        })
    </script>
@endpush
