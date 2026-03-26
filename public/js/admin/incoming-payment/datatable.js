var csrf = $('input[name="_token"]').val();

const IncomingPaymentTable = $("#incoming-payment-table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [[0, "desc"]],
    ajax: {
        url: `${base_url}/incoming-payment-datatable`,
        type: "POST",
        data: {
            _token: csrf,
            from_date: function () {
                return $("#from_date-incoming-payment").val();
            },
            to_date: function () {
                return $("#to_date-incoming-payment").val();
            },
            branch_id: function () {
                return $("#branch-select-incoming-payment").val();
            },
        },
    },
    columns: [
        {
            data: "created_at",
            name: "incoming_payments.created_at",
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
            name: "incoming_payments.date",
        },
        {
            data: "from_name",
            name: "incoming_payments.from_name",
        },
        {
            data: "total",
        },
        {
            data: "reference",
            name: "incoming_payments.reference",
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

$("#set-incoming-payment-table").click(function (e) {
    e.preventDefault();
    IncomingPaymentTable.ajax.reload();
});

$("table").css("width", "100%");
