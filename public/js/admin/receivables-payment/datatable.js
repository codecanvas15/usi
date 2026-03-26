var csrf = $('input[name="_token"]').val();

const ReceivablesPaymentTable = $("table#receivable-payment-table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[0, "desc"]],
    ajax: {
        url: `${base_url}/receivables-payment`,
        type: "get",
        data: {
            _token: csrf,
            customer_id: function () {
                return $("#customer_id-receivable-payment").val();
            },
            from_date: function () {
                return $("#from_date-receivable-payment").val();
            },
            to_date: function () {
                return $("#to_date-receivable-payment").val();
            },
            branch_id: function () {
                return $("#branch-select-receivable-payment").val();
            },
        },
    },
    columns: [
        {
            data: "created_at",
            name: "receivables_payments.created_at",
            searchable: false,
            visible: false,
        },
        {
            data: "DT_RowIndex",
            name: "DT_RowIndex",
            orderable: false,
            searchable: false,
        },
        {
            data: "code",
            name: "bank_code_mutations.code",
        },
        {
            data: "date",
            name: "receivables_payments.date",
        },

        {
            data: "customer_nama",
            name: "customers.nama",
        },
        {
            data: "currency_nama",
            name: "currencies.nama",
        },
        {
            data: "total",
            className: "text-end",
        },
        {
            data: "status",
        },
        {
            data: "action",
            name: "action",
            orderable: false,
            searchable: false,
        },
    ],
});

$("table").css("width", "100%");

$("#set-receivable-payment-table").click(function (e) {
    e.preventDefault();
    ReceivablesPaymentTable.ajax.reload();
});
