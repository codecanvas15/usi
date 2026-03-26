var csrf = $('input[name="_token"]').val();

const table = $("table").DataTable({
    processing: true,
    serverSide: true,
    responsive: true,
    destroy: true,
    order: [
        [0, 'desc']
    ],
    ajax: {
        url: `${base_url}/receive-payment`,
        type: "GET",
        data: {
            _token: csrf,
            from_date: function () {
                return $("#from_date").val();
            },
            to_date: function () {
                return $("#to_date").val();
            },
            branch_id: function () {
                return $("#branch-select").val();
            },
        },
    },
    columns: [
        {
            data: "created_at",
            name: "receive_payments.created_at",
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
            data: "date",
        },
        {
            data: "code",
            name: "receive_payments.code",
        },
        {
            data: "cheque_no",
            name: "receive_payments.cheque_no",
        },
        {
            data: "customer_nama",
            name: "customers.nama",
        },
        {
            data: "due_date",
        },
        {
            data: "realization_date",
        },
        {
            data: "amount",
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
